<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Jobs\ProcessSale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkSession;
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
            $sales = Sale::with('customer')
                ->where('agent_id', $agent->id)
                ->latest()
                ->get();

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
                        $paymentBtn = '
                            <li class="list-inline-item">
                                <a href="javascript:void(0)" data-id="'.$row->id.'" class="action-icon btn-paie">
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

        // 1. Récupérer le panier en session
        $cart = session()->get('sale_cart');
        $isActiveSession = WorkSession::where('user_id',auth()->id())
        ->where('status','open')
        ->first();

        $agentId = auth()->check() ? auth()->id() : $request->agent_id;
        if (! $cart) {
            return response()->json(['status' => false, 'message' => 'Le panier est vide'], 422);
        }
   if (!$isActiveSession) {
        return response()->json([
            'status' => false,
            'message' => 'Erreur : Aucune session de caisse ouverte pour cet utilisateur.'
        ], 403);
    }
        // 2. Validation des données du formulaire
        $request->validate([
            'client_id' => 'required|exists:product_customers,id',
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        $totalAmount = array_sum(array_column($cart, 'subtotal'));
        $workId = $isActiveSession->id;
        // A. Créer l'entête de la vente
        $saleData = [
            'agent_id' => $agentId,
            'work_session_id'=>$workId,
            'customer_id' => $request->client_id,
            'total_amount' => $totalAmount,
            'amount_paid' => $request->amount_paid, // Sera mis à jour par le premier paiement
            'payment_method' => $request->payment_method,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ];
        ProcessSale::dispatch($saleData, $cart,$workId);

        // Vider le panier immédiatement
        session()->forget('sale_cart');

        return response()->json([
            'status' => true,
            'message' => 'Vente en cours de traitement...',
        ]);

    }
}
