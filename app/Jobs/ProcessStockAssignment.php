<?php

namespace App\Jobs;

use App\Models\AgentStock;
use App\Models\Assignment;
use App\Models\Product;
use App\Models\TemporaryReservation;
use App\Models\User;
use App\Notifications\StockAssignementNofication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessStockAssignment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $agentId;

    protected $adminId;

    protected $cart;

    protected $sessionId;

    /**
     * Create a new job instance.
     */
    public function __construct($agentId, $adminId, $cart, $sessionId)
    {
        //
        $this->agentId = $agentId;
        $this->adminId = $adminId;
        $this->cart = $cart;
        $this->sessionId = $sessionId;

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();
        try {
            // 1. Récupération des utilisateurs
            $admin = User::find($this->adminId);
            $agent = User::find($this->agentId);

            foreach ($this->cart as $productId => $item) {
                $product = Product::lockForUpdate()->find($productId);

                if ($product && $product->quantity >= $item['quantity']) {
                    $qty = $item['quantity'];

                    $product->decrement('quantity', $qty);

                    AgentStock::updateOrCreate([
                        'user_id' => $this->agentId,
                        'product_id' => $productId,
                    ], [
                        'price' => $item['price'],
                        'quantity' => DB::raw("quantity + $qty"),
                    ]);

                    Assignment::create([
                        'sender_id' => $this->adminId,
                        'receiver_id' => $this->agentId,
                        'product_id' => $productId,
                        'quantity' => $qty,
                    ]);
                }
            }

            TemporaryReservation::where('session_id', $this->sessionId)->delete();

            // 2. Validation de la base de données AVANT les notifications
            DB::commit();

            // if ($admin && $agent) {
            //     $this->dispatchNotifications($admin, $agent);
            // }

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Erreur Job Stock : '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Méthode isolée pour gérer les routes et l'envoi
     */
    private function dispatchNotifications($admin, $agent)
    {
        // On génère les messages
        $msgAdmin = "Vous avez attribué avec succès un stock à l'agent ".$agent->name;
        $msgAgent = "Un nouveau stock vous a été attribué par l'administrateur ".$admin->name;

        // IMPORTANT : On peut définir la queue 'low' pour les notifications
        // afin qu'elles ne ralentissent pas le traitement des stocks (queue 'high')
        $admin->notify((new StockAssignementNofication($msgAdmin, route('products-stock.index')))
            ->onQueue('low'));

        $agent->notify((new StockAssignementNofication($msgAgent, route('show-stock-agent', ['agent' => $agent->id])))
            ->onQueue('low'));
    }
}
