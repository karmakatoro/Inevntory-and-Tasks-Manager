<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessPayement;
use App\Models\Payement;
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

        // 2. SECRET SENIOR : On lance le Job en arrière-plan
        ProcessPayement::dispatch($payment->id, $customer, $request->amount, $user);

        return response()->json([
            'status' => 'success',
            'message' => 'Le paiement est en cours de traitement par le système.',
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
                            <a class="dropdown-item" href="javascript:void(0);"><i class="mdi mdi-eye-outline me-1"></i>Détails</a>
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
    public function  getCustomerDebts(User $agent){
        if(request()->ajax()){
            $customers = Customer::whereHas('sales',function($q) use ($agent){
                $q->where('agent_id',$agent->id)
                ->whereIn('payment_status',['unpaid',partial]);
            })
            ->withSum(['sales'=> function($q) use ($agent){
                $q->where('agent_id',$agent->id);
            }],total_amount)
            ->withSum(['sales' => function($q) use ($agent) {
                $q->where('agent_id', $agent->id);
            }], 'amount_paid')
            ->get();
            return DataTables::of($customers)
            ->addColumn('customer',fn($row)=>$row->name)
            ->addColumn('total_du',fn($row)=> number_format($row->sales_sum_total_amount,2).'$')
            ->addColumn('total_paye',fn($row)=>number_format($row->sales_sum_amount_paid,2).'$')
            ->addColumn('reste',function($row){
                $reste = $row->sales_sum_total_amount - $row->sales_sum_amount_paid;
                return '<b class="text-danger">' . number_format($reste, 2) . ' $</b>';
            })
            ->addColumn('historique', function($row) {
                // On crée un petit résumé visuel pour chaque vente
                $html = '<ul class="small mb-0">';
                foreach($row->sales as $sale) {
                    if($sale->balance > 0) {
                        $html .= "<li>Facture #{$sale->invoice_number} : <b>{$sale->total_amount}$</b> (Payé: {$sale->amount_paid}$, Reste: <span class='text-warning'>{$sale->balance}$</span>)</li>";
                    }
                }
                $html .= '</ul>';
                return $html;
            })
            ->rawColumns(['reste_a_payer', 'historique', 'action'])
            ->make(true);
        }

    }
}
