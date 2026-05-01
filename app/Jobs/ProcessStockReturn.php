<?php

namespace App\Jobs;
use App\Models\AgentStock;
use App\Models\ProductStock;
use App\Models\Product;
use App\Models\WorkSession;
use App\Models\Payement;
use App\Models\Sale;
use App\Models\User;
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


    /**
     * Create a new job instance.
     */
    public function __construct($agentId,$closeData)
    {
        //
        $this->agentId = $agentId;
        $this->closeData = $closeData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
{
    DB::beginTransaction();
    try {
        $itemReturn = AgentStock::where('user_id', $this->agentId)
            ->where('quantity', '>', 0)
            ->lockForUpdate()
            ->get();

        // Récupérer la session via l'agentId passé au Job
        $sessionClose = WorkSession::where('user_id', $this->agentId)
            ->where('status', 'open')
            ->first();

        if ($sessionClose) {
            // Correction syntaxe update
            $sessionClose->update($this->closeData);
        }

        foreach ($itemReturn as $item) {
            $product = Product::find($item->product_id); // Utilise l'objet $item

            if (!$product) {
                \Log::error("Produit manquant : ID " . $item->product_id);
                continue;
            }

            // Réintégration au stock principal
            $product->increment('quantity', $item->quantity);

            // Tracer le mouvement de retour
            ProductStock::create([
                'product_id' => $product->id,
                'mouvement'  => 'r', // r pour retour
                'price'      => $item->price ?? $product->price,
                'quantity'   => $item->quantity,
                'status'     => 'accepted',
                'user_id'    => $this->agentId
            ]);

            // Vider le stock de l'agent
            $item->update(['quantity' => 0]);
        }

        DB::commit();
    } catch (\Exception $e) {
        DB::rollback();
        \Log::error("Erreur réintégration stock : " . $e->getMessage());
        throw $e;
    }
}
}
