<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPayement;
use App\Models\Payement;
use App\Models\ProductCustomer;
use App\Models\Sale;
use App\Models\User;
use App\Models\WorkSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class PayementController extends Controller
{
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'customer_id' => 'required|exists:product_customers,id',
        ]);

        $isActiveSession = WorkSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if (! $isActiveSession) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur : Aucune session de caisse ouverte.',
            ], 403);
        }
        // 1. Enregistre le paiement brut (Cash-in)
        $payment = Payement::create([
            'amount' => $request->amount,
            'customer_id' => $request->customer_id,
            'payment_method' => $request->payment_method,
            'recorded_by' => auth()->id(),
            'work_session_id' => $isActiveSession->id,

        ]);
        $user = auth()->id();
        $customer = $request->customer_id;
        $workId = $isActiveSession->id;

        // 2. SECRET SENIOR : On lance le Job en arrière-plan
        ProcessPayement::dispatch($payment->id, $customer, $request->amount, $user,$workId);
        $totalSalesAmount = Sale::sum('total_amount');
        $totalPaymentsAmount = Payement::sum('amount');
    
        $newGlobalDebt = $totalSalesAmount - $totalPaymentsAmount;

        return response()->json([
            'status' => 'success',
            'message' => 'Le paiement est en cours de traitement par le système.',
            'newGlobalDebt' => number_format($newGlobalDebt, 2)
        ]);
    }

public function index(User $agent)
{
    // Sécurité : Seul l'admin ou l'agent concerné peut voir
    if (Auth::user()->type !== 'admin' && Auth::id() !== $agent->id) {
        abort(403, 'Action non autorisée.');
    }

    if (request()->ajax()) {
        // Chargement des relations (Eager Loading) pour éviter les requêtes N+1
        $query = Payement::with(['allocations.sale.customer', 'agent']);

        // Filtrage strict par agent
        if (Auth::user()->type !== 'admin') {
            $query->where('recorded_by', Auth::id());
        } else {
            $query->where('recorded_by', $agent->id);
        }

        $payments = $query->latest()->get();

        return DataTables::of($payments)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($row) {
                return '<div class="form-check">
                            <input type="checkbox" class="form-check-input row-checkbox" value="'.$row->id.'">
                        </div>';
            })
            ->addColumn('date', function ($row) {
                return $row->created_at->format('d/m/Y H:i');
            })
            ->addColumn('ventes', function ($row) {
                // Style Odoo : Uniquement les allocations avec argent réel
                $allocations = $row->allocations->where('amount_allocated', '>', 0);
                
                if ($allocations->isEmpty()) {
                    return '<span class="badge bg-soft-warning text-warning">Acompte / Non alloué</span>';
                }

                if ($allocations->count() === 1) {
                    $invoice = $allocations->first()->sale->invoice_number ?? 'Inconnu';
                    return '<span class="badge bg-soft-info text-info"><i class="mdi mdi-file-document-outline me-1"></i>' . $invoice . '</span>';
                }

                return '<span class="badge bg-soft-primary text-primary" data-bs-toggle="tooltip" title="Réparti sur plusieurs factures">
                            <i class="mdi mdi-layers-outline me-1"></i>' . $allocations->count() . ' Factures
                        </span>';
            })
            ->addColumn('customer', function ($row) {
                // On cherche le client dans la première allocation valide
                $firstAlloc = $row->allocations->where('amount_allocated', '>', 0)->first() 
                              ?? $row->allocations->first();
                return $firstAlloc->sale->customer->name ?? 'Client inconnu';
            })
            ->addColumn('amount', function ($row) {
                return '<span class="fw-bold text-dark">' . number_format($row->amount, 2) . ' $</span>';
            })
            ->addColumn('status', function ($row) {
                return '<span class="badge badge-success-lighten"><i class="mdi mdi-check-circle me-1"></i>Encaissé</span>';
            })
            ->addColumn('recorded_by', function ($row) {
                return $row->agent->name ?? 'N/A';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">Action</button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="javascript:void(0);" data-id='.$row->id.'"><i class="mdi mdi-eye-outline me-1"></i>Détails</a>
                            <a class="dropdown-item text-danger delete-payment" data-id="'.$row->id.'" href="javascript:void(0);">
                                <i class="mdi mdi-trash-can-outline me-1"></i>Supprimer
                            </a>
                        </div>
                    </div>';
            })
            ->rawColumns(['checkbox', 'ventes', 'amount', 'status', 'action'])
            ->make(true);
    }

    return view('pages.payements.index', compact('agent'));
}
    public function getCustomerDebts(User $agent) {

    if (request()->ajax()) {
        $isAdmin = Auth::user()->type==="admin";
        $isConnected = Auth::id();
        $customers = ProductCustomer::whereHas('sales', function($q) use ($agent,$isAdmin) {
            if(!$isAdmin){
                $q->where('agent_id', $agent->id);
            }
                
             $q->whereIn('payment_status', ['unpaid', 'partial']);
            })
            ->with(['sales' => function($q) use ($agent,$isAdmin) { 
                if(!$isAdmin){
                    $q->where('agent_id', $agent->id);
                }
                
            }])
            ->withSum(['sales' => function($q) use ($agent,$isAdmin) {
                if(!$isAdmin){
                    $q->where('agent_id', $agent->id);
                }
                
            }], 'total_amount')
            ->withSum(['sales' => function($q) use ($agent,$isAdmin) {
               if(!$isAdmin){
                    $q->where('agent_id', $agent->id);
                }
            }], 'amount_paid')
            ->get();

        return DataTables::of($customers)
            ->addColumn('customer', fn($row) => $row->name)
            ->addColumn('total_du', fn($row) => number_format($row->sales_sum_total_amount, 2) . '$')
            ->addColumn('total_paye', fn($row) => number_format($row->sales_sum_amount_paid, 2) . '$')
            ->addColumn('reste', function($row) {
                $reste = $row->sales_sum_total_amount - $row->sales_sum_amount_paid;
                return '<b class="text-danger">' . number_format($reste, 2) . ' $</b>';
            })
           
            ->addColumn('action', function($row) {
    $reste = $row->sales_sum_total_amount - $row->sales_sum_amount_paid;
    
    // Historique préparé pour la modal
    $historyHtml = '<ul class="list-group list-group-flush">';
    foreach($row->sales as $sale) {
        $balance = $sale->total_amount - $sale->amount_paid;
        if($balance > 0) {
            $historyHtml .= "<li class='list-group-item d-flex justify-content-between align-items-center'>
                <span>Facture #{$sale->invoice_number}</span>
                <span class='badge bg-soft-warning text-warning'>Reste: ".number_format($balance, 2)."$</span>
            </li>";
        }
    }
    $historyHtml .= '</ul>';

    return '
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-primary btn-paie me-1" 
                data-customer="'.$row->id.'" 
                data-debt="'.number_format($reste, 2).'" 
                title="Payer">
                <i class="mdi mdi-cash-multiple me-1"></i> Payer
            </button>
            
            <button type="button" class="btn btn-sm btn-info btn-details me-1" 
                data-name="'.$row->name.'"
                data-history="'.htmlspecialchars($historyHtml).'" 
                title="Détails">
                <i class="mdi mdi-eye-outline me-1"></i> Détails
            </button>

            <button type="button" class="btn btn-sm btn-danger btn-cancel" 
                data-id="'.$row->id.'" 
                title="Annuler">
                <i class="mdi mdi-close-circle-outline me-1"></i> Annuler
            </button>
        </div>';
})
            ->rawColumns(['reste', 'action'])
            ->make(true);
    }


    
    $isAdmin = Auth::user()->type === 'admin';
    $isConnected = Auth::id();
   $query = \DB::table('sales')
    ->whereIn('payment_status', ['unpaid', 'partial']);

// Si ce n'est pas un admin, on filtre strictement par l'ID de l'agent
if (!$isAdmin) {
    $query->where('agent_id', $isConnected);

} 
// Note : Si vous voulez que l'admin voie la dette d'un agent spécifique 
// via une route dédiée, utilisez : else { $query->where('agent_id', $agent->id); }

$totalGlobalDebt = $query->selectRaw('SUM(total_amount) - SUM(amount_paid) as net_debt')
    ->value('net_debt') ?? 0;

    return view('pages.payements.credit', compact('totalGlobalDebt'));
}
public function getPayementAllocation($id){
    $payement = payement::with(['allocations.sale'])->findOrFail($id);
   
    $html = '<div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>N° Facture</th>
                            <th class="text-end">Montant Alloué</th>
                            <th class="text-center">Date Vente</th>
                        </tr>
                    </thead>
                    <tbody>';
    foreach($payement->allocations as $allocation){
        $html .= '<tr>
                    <td>
                        <span class="fw-bold text-primary">#' . $allocation->sale->invoice_number . '</span>
                    </td>
                    <td class="text-end fw-medium">' . number_format($allocation->amount_allocated, 2) . ' $</td>
                    <td class="text-center">' . \Carbon\Carbon::parse($allocation->sale->created_at)->format('d/m/Y') . '</td>
                  </tr>';
    }
    $html .= '</tbody>
              <tfoot class="table-light">
                <tr>
                    <td class="fw-bold">TOTAL ALLOUÉ</td>
                    <td class="text-end fw-bold text-success">' . number_format($payement->amount, 2) . ' $</td>
                    <td></td>
                </tr>
              </tfoot>
            </table>
          </div>';
          // Au lieu de return response()->json($html);
return response($html, 200)->header('Content-Type', 'text/html');;
}
}