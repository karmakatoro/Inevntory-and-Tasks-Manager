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

            // 3. Envoi des notifications APRÈS le commit (plus sûr)
            $this->sendNotifications($admin, $agent);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Erreur Job Stock : '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Méthode isolée pour gérer les routes et l'envoi
     */
    private function sendNotifications($admin, $agent)
    {
        try {
            if ($admin && $agent) {
                // Route Admin
                $urlAdmin = route('products-stock.index');
                $msgAdmin = "Vous avez attribué avec succès un stock à l'agent ".$agent->name;
                $admin->notify(new StockAssignementNofication($msgAdmin, $urlAdmin));

                // Route Agent (Vérifie bien que le paramètre est 'agent' dans web.php)
                $urlAgent = route('show-stock-agent', ['agent' => $agent->id]);
                $msgAgent = "Un nouveau stock vous a été attribué par l'administrateur ".$admin->name;
                $agent->notify(new StockAssignementNofication($msgAgent, $urlAgent));
            }
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la génération des notifications : '.$e->getMessage());
            // On ne throw pas l'erreur ici pour ne pas marquer le Job comme FAIL
            // si seul le mail a échoué mais que la base de données est OK.
        }
    }
}
