<?php
namespace App\Jobs;

use App\Models\Product; 
use App\Models\ProductStock; 
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessStockValidate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $session;
    protected $userId;
    protected $qtyReturnedPhysical;

    /**
     * Create a new job instance.
     *
     * @param mixed $session
     * @param int $userId
     * @param array $qtyReturnedPhysical
     */
    public function __construct($session, $userId, array $qtyReturnedPhysical)
    {
        $this->session = $session;
        $this->userId = $userId;
        $this->qtyReturnedPhysical = $qtyReturnedPhysical;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // 1. Initialiser la transaction de la base de données
            DB::beginTransaction();

            // 2. Chargement de la relation (Eager Loading)
            $this->session->load('assignment.product');
            
            $totalLossValueSession = 0;

            foreach ($this->session->assignment as $item) {
                // Récupération de la quantité physique entrée (ou 0 par défaut si non soumis)
                $physicalQty = $this->qtyReturnedPhysical[$item->id] ?? 0;
                
                // Quantité théorique attendue par le système
                $systemQty = $item->quantity_returned; 
                
                // Écart : Physique - Système (Ex: 8 - 10 = -2)
                $discrepancy = $physicalQty - $systemQty;
                $lossValue = 0;

                // Calcul de la valeur financière si un manquant est détecté
                if ($discrepancy < 0) {
                    $productPrice = $item->product->price ?? 0;
                    $lossValue = abs($discrepancy) * $productPrice;
                    $totalLossValueSession += $lossValue;
                }

              
     \DB::table('assignments')
    ->where('id', $item->id)
    ->update([
        'physical_quantity' => $physicalQty,
        'stock_discrepancy' => $discrepancy,
        'loss_value'        => $lossValue,
        'updated_at'        => now(), // Optionnel : pour garder tes logs à jour
    ]);

                // Détermination de la quantité réelle à réintégrer au stock central
                // Si l'agent a ramené quelque chose (> 0), on prend sa valeur. 
                // Sinon, s'il a ramené 0 mais que le système n'attendait rien, on ne fait rien.
                $qtyToReintegrate = ($physicalQty > 0) ? $physicalQty : (($systemQty > 0 && $physicalQty == 0) ? 0 : 0);
                
                // Si le physique est à 0 mais que le système attendait du stock, on ne réintègre rien au dépôt (c'est une perte sèche)
                // Donc on ne réintègre que si $physicalQty > 0
                if ($physicalQty > 0) {
                    $product = $item->product; 
                    
                    // Incrémentation du stock central
                    $product->increment('quantity', $physicalQty);

                    // 3. Enregistrement du mouvement dans le journal
                    ProductStock::create([
                        'product_id' => $product->id, 
                        'mouvement'  => 'r', // 'r' pour Retour/Réintégration
                        'quantity'   => $physicalQty, // Correction de la variable inexistante
                        'price'      => $product->price,
                        'status'     => 'accepted',
                        'user_id'    => $this->userId, 
                    ]);
                }
            } 

            // 4. Mise à jour du statut de la session et injection de la perte globale
            $this->session->update([
                'status'           => 'stock_valide',
                'total_loss_value' => $totalLossValueSession, // Utile pour tes futurs calculs de CA global
            ]);

            // 5. Valider la transaction
            DB::commit();
            
            Log::info("Job réussi : Réintégration des stocks effectuée pour la session ID " . $this->session->id);

        } catch (\Exception $e) {
            // Annuler tout en cas d'erreur
            DB::rollBack();
            Log::error("Échec du Job de réintégration pour la session " . $this->session->id . " : " . $e->getMessage());
            
            throw $e;
        }
    }
}