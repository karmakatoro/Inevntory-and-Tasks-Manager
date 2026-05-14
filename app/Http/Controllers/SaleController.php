<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessSale;
use App\Models\Sale;
use App\Models\User;
use App\Models\WorkSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class SaleController extends Controller
{
    //
    public function index(User $agent)
    {
        if (Auth::user()->type !== 'admin' && Auth::id() !== $agent->id) {
            abort(403, 'Action non autorisée.');
        }

        if (request()->ajax()) {
            // On filtre par l'ID de l'agent reçu en paramètre
            $query = Sale::with(['customer' => function ($q) {
                // On charge les ventes liées pour que l'Accessor total_debt fonctionne
                $q->with('sales');
            }]);
            if(Auth::user()->type !== "admin"){
                $query->where('agent_id', Auth::id());
            }
            $sales = $query->latest()->get();
                

            return DataTables::of($sales)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return '<div class="form-check font-16 mb-0">
                            <input class="form-check-input check-row" name="single-row" value="'.$row->id.'" type="checkbox" id="sale'.$row->id.'">
                            <label class="form-check-label" for="product'.$row->id.'">&nbsp;</label>
                        </div>';
                })
                ->addColumn('reference', function ($row) {
                    // Prix venant du modèle Product lié
                    return $row->invoice_number;
                })
                ->addColumn('customer', function ($row) {
                    $name = $row->customer->name ?? 'Client inconnu';

                    return $name;
                })
                ->addColumn('montantTotal', function ($row) {

                    return number_format($row->total_amount, 2).' $';
                })
                ->addColumn('montantPaye', function ($row) {

                    $amount_paid = $row->amount_paid ?? 0;

                    return '$'.number_format($amount_paid, 2);
                })
                ->addColumn('reste', function ($row) {

                    $balance = $row->balance ?? 0;

                    return '$'.number_format($balance, 2);
                })
                ->addColumn('status', function ($row) {
                    if ($row->payment_status === 'unpaid') {
                        return '<span class="badge bg-soft-danger text-danger">'.$row->payment_status.'</span>';
                    } elseif ($row->payment_status === 'partial') {
                        return '<span class="badge bg-soft-warning text-warning">'.$row->payment_status.'</span>';
                    }

                    return '<span class="badge bg-soft-success text-success">'.$row->payment_status.'</span>';
                })
                ->addColumn('action', function ($row) {
                    $paymentBtn = '';
                    if ($row->payment_status === 'unpaid' || $row->payment_status === 'partial') {
                        // On récupère les valeurs depuis le modèle
                        $totalDebt = $row->customer->total_debt ?? 0;
                        $clientId = $row->customer_id ?? 0;
                        $lastAllocated = $row->customer->last_payment_amount;
                        $paymentBtn = '
            <li class="list-inline-item">
                <a href="javascript:void(0)" 
                   data-customer="'.$clientId.'" 
                   data-debt="'.$totalDebt.'" 
                   data-last ="'.$lastAllocated.'"
                   class="action-icon btn-paie">
                    <i class="mdi mdi-check-circle text-success"></i> Payer
                </a>
            </li>';
                    }

                    return $actionBtn = '
                    <ul class="list-inline mb-0">
                    '.$paymentBtn.'
                    </ul>
                    ';
                })
                ->rawColumns(['checkbox', 'reference', 'customer', 'montantTotal', 'montantPaye', 'rest', 'status', 'action'])
                ->make(true);
        }

        // Très important : cette ligne doit être en dehors du bloc "if (request()->ajax())"
        // pour que la page s'affiche lors de la première visite (non-AJAX)
        return view('pages.sales.agent.index', compact('agent'));
    }

    public function store(Request $request)
    {
        // 1. Récupérer et valider immédiatement les données nécessaires
        $cart = session()->get('sale_cart');

        if (! $cart) {
            return response()->json(['status' => false, 'message' => 'Le panier est vide'], 422);
        }

        $isActiveSession = WorkSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        if (! $isActiveSession) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur : Aucune session de caisse ouverte.',
            ], 403);
        }

        $request->validate([
            'client_id' => 'required|exists:product_customers,id',
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        // 2. Préparation des données
        $totalAmount = array_sum(array_column($cart, 'subtotal'));
        $agentId = auth()->id() ?? $request->agent_id;
        $workId = $isActiveSession->id;

        $saleData = [
            'agent_id' => $agentId,
            'work_session_id' => $workId,
            'customer_id' => $request->client_id,
            'total_amount' => $totalAmount,
            'amount_paid' => $request->amount_paid,
            'payment_method' => $request->payment_method,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ];

        // 3. Vider le panier EN PREMIER pour libérer la session
        session()->forget('sale_cart');

        // 4. Dispatch avec afterCommit pour éviter le Lock Timeout
        ProcessSale::dispatch($saleData, $cart, $workId)->afterCommit();

        return response()->json([
            'status' => true,
            'message' => 'Vente envoyée au système de traitement.',
        ]);
    }
}
