<?php

namespace App\Jobs;

use App\Models\PayementAllocation;
use App\Models\Sale;
use App\Models\CashMovement;
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
    protected $workId;

    public function __construct($payementId,$customerId, $amount, $userId,$workId)
    {
        $this->customerId = $customerId;
        $this->payementId = $payementId;
        $this->workId = $workId;
        $this->amount = $amount;
        $this->userId = $userId; // Reçu du Controller
    }

    // ... début du code identique ...

    public function handle(): void
    {
        DB::transaction(function () {
            $remaining = $this->amount;
            $impactedInvoices = []; // Pour stocker les numéros de factures

            $unpaidSales = Sale::where('customer_id', $this->customerId)
                ->whereIn('payment_status', ['unpaid', 'partial'])
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($unpaidSales as $sale) {
                if ($remaining <= 0) break;

                $debt = $sale->balance;
                $allocation = min($remaining, $debt);
                
                // On garde une trace du numéro de facture
                $impactedInvoices[] = $sale->invoice_number;

                $allocationEntry = PayementAllocation::create([
                    'payment_id' => $this->payementId,
                    'user_id' => $this->userId,
                    'amount_allocated' => $allocation,
                    'sale_id' => $sale->id,
                    'note' => "Affectation automatique FIFO sur facture {$sale->invoice_number}.",
                ]);

                if ($allocationEntry) {
                    $sale->updatePayementStatus();
                    $remaining -= $allocation;
                }
            }

            // CORRECTION ICI : On utilise les numéros collectés
            $invoiceList = implode(', ', $impactedInvoices);

            CashMovement::create([
                'work_session_id' => $this->workId,
                'user_id' => $this->userId, 
                'type' => 'in',
                'amount' => $this->amount,
                'category' => 'paie_cash', 
                'description' => "Recouvrement pour les factures : " . ($invoiceList ?: 'N/A'),
            ]);
        });
    }
}
