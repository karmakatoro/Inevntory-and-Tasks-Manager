<?php

namespace App\Jobs;

use App\Models\AgentStock;
use App\Models\Payement;
use App\Models\PayementAllocation;
use App\Models\ProductCustomer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use App\Notifications\StockAssignementNofication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessSale implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $saleData;

    protected $cart;

    protected $workId;

    public function __construct($saleData, $cart, $workId)
    {
        $this->cart = $cart;
        $this->saleData = $saleData;
        $this->workId = $workId;
    }

    // ... (imports identiques)

    public function handle(): void
    {
        DB::beginTransaction();
        try {
            // 1. Création de la vente
            $sale = Sale::create($this->saleData);
            $customer = ProductCustomer::find($this->saleData['customer_id']);
            $agent = User::find($this->saleData['agent_id']);
            $paie = null;

            // 2. Traitement des articles et Stock
            foreach ($this->cart as $id => $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'work_session_id' => $this->workId,
                    'product_id' => $id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);

                $agentStock = AgentStock::where('user_id', $this->saleData['agent_id'])
                    ->where('product_id', $id)
                    ->lockForUpdate()
                    ->first();

                if (! $agentStock || $agentStock->quantity < $item['quantity']) {
                    throw new \Exception('Stock insuffisant pour : '.$item['name']);
                }

                $agentStock->decrement('quantity', $item['quantity']);
            }

            // 3. Gestion du paiement ET de l'allocation (MODIFIÉ ICI)
            if ($this->saleData['amount_paid'] > 0) {
                $payment = Payement::create([
                    'customer_id' => $this->saleData['customer_id'],
                    'work_session_id' => $this->workId,
                    'amount' => $this->saleData['amount_paid'],
                    'payment_method' => $this->saleData['payment_method'],
                    'recorded_by' => $this->saleData['agent_id'],
                ]);

                // On crée l'allocation
                PayementAllocation::create([
                    'sale_id' => $sale->id,
                    'payment_id' => $payment->id,
                    'user_id' => $this->saleData['agent_id'],
                    'amount_allocated' => $this->saleData['amount_paid'],
                ]);

            }
            DB::commit();

            $this->sendNotification($agent, $paie);
            $this->sendNotification(
                $agent,
                $paie,
                $customer,
                $sale // L'objet Sale que tu viens de créer/mettre à jour
            );

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur de Processus Vente : '.$e->getMessage());
            throw $e;
        }
    }

    private function sendNotification($agent, $paie, $client = null, $sale = null)
    {
        try {
            // Notification pour l'Agent (on garde ta logique actuelle)
            if ($agent) {
                $msgAgent = $paie
                    ? "Vente enregistrée. Paiement reçu : {$paie->amount} USD."
                    : 'Vente à crédit enregistrée avec succès.';
               $urlAgent = route('sales.index', ['agent' => $agent->id]);
                $agent->notify(new StockAssignementNofication($msgAgent, $urlAgent));
            }

            // Notification pour le Client (Finances de la vente)
            if ($client && $sale) {
                $sale->refresh();
                $total = number_format($sale->total_amount, 2);
                $paye = number_format($sale->amount_paid, 2);
                $reste = number_format($sale->balance, 2);

                $msgClient = "Détails de votre achat :\n";
                $msgClient .= "- Total : {$total} USD\n";
                $msgClient .= "- Montant versé : {$paye} USD\n";

                if ($sale->balance > 0) {
                    $msgClient .= "- Reste à payer : {$reste} USD (État : Partiel/Crédit)";
                } else {
                    $msgClient .= '- État : Entièrement payé. Merci !';
                }

                // Pour le client, pas de lien (URL null ou vide)
                $client->notify(new StockAssignementNofication($msgClient, null));
            }

        } catch (\Exception $e) {
            \Log::error("Erreur d'envoi de notification client : ".$e->getMessage());
        }
    }
}
