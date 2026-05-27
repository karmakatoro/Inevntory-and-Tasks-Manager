<?php

namespace App\Jobs;

use App\Models\AgentStock;
use App\Models\WorkSession;
use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

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
        DB::beginTransaction();
        try {
            // 1. Mettre à jour le statut de la session en attente de validation par les responsables
            $sessionClose = WorkSession::where('id', $this->workSessionId)->first();

            if ($sessionClose) {
                // On fusionne le changement de statut pour la file d'attente
                $data = array_merge($this->closeData); 
                // Note : Laisse 'open' ou mets 'en_attente' selon ton choix pour la file d'attente du Journal
                $sessionClose->update($data);
            }

            // 2. Récupérer tout ce que l'agent possède actuellement dans sa sacoche
            $agentItems = AgentStock::where('user_id', $this->agentId)
                ->where('quantity', '>', 0)
                ->get();

            foreach ($agentItems as $item) {
                // On inscrit simplement la quantité théorique que l'agent prétend retourner
                $assignment = Assignment::where('work_session_id', $this->workSessionId)
                    ->where('product_id', $item->product_id)
                    ->where('receiver_id', $this->agentId)
                    ->first();

                if ($assignment) {
                    $assignment->update([
                        'quantity_returned' => $item->quantity ,// Déclaration brute de l'agent
                        'closed_at'=> now(),
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error("Erreur lors de la pré-clôture de l'agent : " . $e->getMessage());
            throw $e;
        }
    }
}