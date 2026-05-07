<?php

namespace App\Jobs;

use App\Models\PayementAllocation;
use App\Models\Sale;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

// ... imports identiques ...

class ProcessPayement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $customerId;

    protected $payementId;

    protected $amount;

    protected $userId; // On ajoute l'ID de l'utilisateur ici

    public function __construct($payementId,$customerId, $amount, $userId)
    {
        $this->customerId = $customerId;
        $this->payementId = $payementId;
        $this->amount = $amount;
        $this->userId = $userId; // Reçu du Controller
    }

    public function handle(): void
    {
        // Pas besoin de DB::commit() manuel si tu utilises la closure DB::transaction
        DB::transaction(function () {
            $remaining = $this->amount;

            $unpaidSales = Sale::where('customer_id', $this->customerId)
                ->whereIn('payment_status', ['unpaid', 'partial'])
                ->orderBy('created_at', 'asc')
                ->get();
            \Log::info("Job FIFO - Client: {$this->customerId}, Ventes trouvées: ".$unpaidSales->count().", Montant: {$remaining}");
            foreach ($unpaidSales as $sale) {
                if ($remaining <= 0) {
                    break;
                }

                $debt = $sale->balance;
                $allocation = min($remaining, $debt);

                $note = "Affectation automatique FIFO sur facture {$sale->invoice_number}.";

                $allocationEntry = PayementAllocation::create([
                    'payment_id' => $this->payementId,
                    'user_id' => $this->userId, // Utilisation de l'ID passé au constructeur
                    'amount_allocated' => $allocation,
                    'sale_id' => $sale->id,
                    'note' => $note,
                ]);

                if ($allocationEntry) {
                    $sale->updatePayementStatus();
                    $remaining -= $allocation;
                }
            }
        });
        // Note : La transaction gère le commit/rollback automatiquement ici
    }
}
