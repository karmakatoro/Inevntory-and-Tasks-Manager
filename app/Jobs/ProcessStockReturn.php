<?php

namespace App\Jobs;

use App\Models\AgentStock;
use App\Models\Assignment;
use App\Models\WorkSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessStockReturn implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $agentId;
    protected $closeData;
    protected $workSessionId;

    /**
     * Create a new job instance.
     */
    public function __construct($agentId, $closeData, $workSessionId)
    {
        $this->agentId = $agentId;
        $this->closeData = $closeData;
        $this->workSessionId = $workSessionId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            DB::beginTransaction();

            // 1. Mettre à jour le statut de la session
            $sessionClose = WorkSession::where('id', $this->workSessionId)->first();

            if ($sessionClose) {
                // 🛠️ FIX : Plus besoin du array_merge défectueux
                $sessionClose->update($this->closeData);
            }

            // 2. Récupérer tout ce que l'agent possède actuellement dans sa sacoche
            $agentItems = AgentStock::where('user_id', $this->agentId)
                ->where('quantity', '>', 0)
                ->get();

            foreach ($agentItems as $item) {
                // On cherche l'assignation correspondante
                $assignment = Assignment::where('work_session_id', $this->workSessionId)
                    ->where('product_id', $item->product_id)
                    ->where('receiver_id', $this->agentId)
                    ->first();

                if ($assignment) {
                    // 🛠️ FIX : Mise à jour directe via DB::table pour éviter les bugs de clés composites / pivots
                    DB::table('assignments')
                        ->where('id', $assignment->id)
                        ->update([
                            'quantity_returned' => $item->quantity, // Déclaration brute
                            'closed_at'         => now(),
                        ]);

                    // 🛠️ FIX : On vide la sacoche de l'agent (décrémentation complète)
                    $item->decrement('quantity', $item->quantity);
                    
                    Log::info("Sacoche Agent Stock décrémentée pour le produit ID: {$item->product_id}");
                }
            }

            DB::commit();
            Log::info("ProcessStockReturn exécuté avec succès pour la session : {$this->workSessionId}");
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Erreur lors de la pré-clôture de l'agent : ".$e->getMessage());
            throw $e;
        }
    }
}