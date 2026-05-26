<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\TemporaryReservation;
use App\Models\Assignment;
use App\Notifications\StockAssignementNofication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessStockAssignment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $agentId;

    protected $adminId;

    protected $cart;

    protected $sessionId;

    protected $workSessionId;

    /**
     * Create a new job instance.
     */
    public function __construct($agentId, $adminId, $cart, $sessionId,$workSessionId)
    {
        //
        $this->agentId = $agentId;
        $this->adminId = $adminId;
        $this->cart = $cart;
        $this->workSessionId = $workSessionId;
        $this->sessionId = $sessionId;

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \DB::beginTransaction();
        try {
            // 1. Calcul du PROCHAIN numéro unique pour TOUT le panier avant d'entrer dans la boucle
            $lastAssignment = Assignment::whereYear('created_at', date('Y'))->latest('id')->first();
            $nextNumber = $lastAssignment ? (int) substr($lastAssignment->reference_bon, -4) + 1 : 1;
            $uniqueReferenceBon = 'BDS-'.date('Y').'-'.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            foreach ($this->cart as $productId => $item) {
                $product = Product::lockForUpdate()->find($productId);

                if ($product && $product->quantity >= $item['quantity']) {
                    $qty = $item['quantity'];
                    $product->decrement('quantity', $qty);

                    // 2. On passe la même référence à chaque ligne
                    Assignment::create([
                        'reference_bon' => $uniqueReferenceBon, // Le même pour tout le groupe !
                        'sender_id' => $this->adminId,
                        'receiver_id' => $this->agentId,
                        'work_session_id'=>$this->workSessionId,
                        'quantity'=>$qty,
                        'product_id' => $productId,
                        'status' => 'en_attente',
                    ]);
                }
            }

            TemporaryReservation::where('session_id', $this->sessionId)->delete();
            \DB::commit();

        } catch (\Exception $e) {
            \DB::rollback();
            throw $e;
        }
    }

    /**
     * Méthode isolée pour gérer les routes et l'envoi
     */
    // private function dispatchNotifications($admin, $agent)
    // {
    //     // On génère les messages
    //     $msgAdmin = "Vous avez attribué avec succès un stock à l'agent ".$agent->name;
    //     $msgAgent = "Un nouveau stock vous a été attribué par l'administrateur ".$admin->name;

    //     // IMPORTANT : On peut définir la queue 'low' pour les notifications
    //     // afin qu'elles ne ralentissent pas le traitement des stocks (queue 'high')
    //     $admin->notify((new StockAssignementNofication($msgAdmin, route('products-stock.index')))
    //         ->onQueue('low'));

    //     $agent->notify((new StockAssignementNofication($msgAgent, route('show-stock-agent', ['agent' => $agent->id])))
    //         ->onQueue('low'));
    // }
}
