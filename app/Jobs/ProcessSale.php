<?php

namespace App\Jobs;
use App\Notifications\StockAssignementNofication;
use App\Models\AgentStock;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Models\Payement; 

class ProcessSale implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $saleData;
    protected $cart;

    public function __construct($saleData, $cart)
    {
        $this->cart = $cart;
        $this->saleData = $saleData;
    }

    public function handle(): void
    {
        DB::beginTransaction();
        try {
            $sale = Sale::create($this->saleData);
            $agent = User::find($this->saleData['agent_id']);
            $paie = null;

            foreach ($this->cart as $id => $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);

                $agentStock = AgentStock::where('user_id', $this->saleData['agent_id'])
                    ->where('product_id', $id)
                    ->first();

                if (! $agentStock || $agentStock->quantity < $item['quantity']) {
                    throw new \Exception('Stock insuffisant pour : '.$item['name']);
                }
                $agentStock->decrement('quantity', $item['quantity']);
            }

            // Enregistrer le paiement
            if ($this->saleData['amount_paid'] > 0) {
                $paie = Payement::create([ // Correction de l'orthographe
                    'sale_id' => $sale->id,
                    'amount' => $this->saleData['amount_paid'],
                    'payment_method' => $this->saleData['payment_method'],
                    'recorded_by' => $this->saleData['agent_id'],
                ]);
            }

            DB::commit();

            // Appel de la notification après le commit
            $this->sendNotification($agent, $paie);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error("Erreur de Processus Vente : " . $e->getMessage());
            throw $e; 
        }
    }

    private function sendNotification($agent, $paie)
    {
        try {
            if ($agent) {
                
                if ($paie) {
                    $msgVente = "Votre vente a été enregistrée avec succès. Paiement reçu : " . $paie->amount . " USD.";
                } else {
                    $msgVente = "Votre vente a été enregistrée avec succès (Vente à crédit).";
                }

                $url = route('sales.index' ,['agent'=>$agent->id]); // Souvent les listes n'ont pas besoin d'ID dans l'URL
                 $agent->notify(new StockAssignementNofication($msgVente, $url));
            }
        } catch (\Exception $e) {
            \Log::error("Erreur d'envoi de mail vente : " . $e->getMessage());
        }
    }
}