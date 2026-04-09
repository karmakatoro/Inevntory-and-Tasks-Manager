<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payement;
use App\Models\AgentStock;
use Illuminate\Support\Facades\DB;
class SaleController extends Controller
{
    //
    public function index(User $agent){
        if(Auth::user()->type !=='admin'&& Auth::id() !== $agent->id ){
                abort(403,"Action non autorisée.");
            }
        if (request()->ajax()) {
            // On filtre par l'ID de l'agent reçu en paramètre


            $sales = Sale::with(['client','user'])
                ->where('user_id', $agent->id)
                ->latest()
                ->get();

            return DataTables::of($sales)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return '<div class="form-check font-16 mb-0">
                            <input class="form-check-input check-row" name="single-row" value="'.$row->id.'" type="checkbox" id="product'.$row->id.'">
                            <label class="form-check-label" for="product'.$row->id.'">&nbsp;</label>
                        </div>';
                })
                 ->addColumn('Reference', function ($row) {
                    // Prix venant du modèle Product lié
                    $price = $row->invoice_number ?? 0;

                    return $price;
                })
                ->addColumn('customer', function ($row) {
                    $name = $row->client->name ?? 'Client inconnu';

                    return $name;
                })
                ->addColumn('MontantTotal', function ($row) {
                    
                    $total_amount = $row->total_amount ?? 0;

                    return '$'.number_format($total_amount, 2);
                })
                  ->addColumn('MontantPaye', function ($row) {
                    
                    $amount_paid = $row->amount_paid ?? 0;

                    return '$'.number_format($amount_paid, 2);
                })
               ->addColumn('Reste', function ($row) {
                    
                    $balance = $row->balance ?? 0;

                    return '$'.number_format($balance, 2);
                })
                ->addColumn('Status', function ($row) {
                    if ($row->payment_status === 'unpaid') {
                        return '<span class="badge bg-soft-danger text-danger">'.$row->payment_status.'</span>';
                    } elseif ($row->payment_status === 'partial') {
                        return '<span class="badge bg-soft-warning text-warning">'.$row->payment_status.'</span>';
                    }

                    return '<span class="badge bg-soft-success text-success">'.$row->payment_status.' en stock</span>';
                })
                ->addColumn('action', function ($row) {
                    $paymentBtn = '';
                    if($row->payment_status === 'unpaid' || $row->payment_status === 'partial'){
                        $paymentBtn = '
                            <li class="list-inline-item">
                                <a href="javascript:void(0)" data-id="'.$row->id.'" class="action-icon btn-select">
                                    <i class="mdi mdi-check-circle text-success"></i> Sélectionner
                                </a>
                            </li>';
                    }
                    return $actionBtn = '
                    <ul class="list-inline mb-0">
                    '.$paymentBtn.'
                    </>
                    ';
                })
                ->rawColumns(['checkbox','reference', 'Customer', 'MontantTotal', 'MontantPaye','Reste', 'action'])
                ->make(true);
        }

        // Très important : cette ligne doit être en dehors du bloc "if (request()->ajax())"
        // pour que la page s'affiche lors de la première visite (non-AJAX)
        return view('pages.sale-agent.index', compact('agent'));
    }
    
    public function store (Request $request){

    {
        // 1. Récupérer le panier en session
        $cart = session()->get('sale_cart');
        $agentId = auth()->check()?auth()->id():$request->agent_id;
        if (!$cart || count($cart) == 0) {
            return response()->json(['status' => false, 'message' => 'Le panier est vide'], 422);
        }

        // 2. Validation des données du formulaire
        $request->validate([
            'client_id' => 'required|exists:product_customers,id',
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'agent_id'=>'required',
            'latitude'=>'numeric',
            'longitude'=>'numeric'

        ]);

        $totalAmount = array_sum(array_column($cart, 'subtotal'));
        $amountPaid = $request->amount_paid;

        try {
            return DB::transaction(function () use ($cart, $totalAmount, $amountPaid, $request,$agentId) {

                // A. Créer l'entête de la vente
                $sale = Sale::create([
                    'agent_id' => $agentId,
                    'customer_id' => $request->client_id,
                    'total_amount' => $totalAmount,
                    'amount_paid' => 0, // Sera mis à jour par le premier paiement
                    'payment_method' => $request->payment_method,
                    'latitude'=>$request->latitude,
                    'longitude'=>$request->longitude
                    // 'invoice_number' est géré par le hook du modèle qu'on a vu
                ]);

                // B. Enregistrer les produits et diminuer le stock Agent
                foreach ($cart as $id => $item) {
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                        'subtotal' => $item['subtotal'],
                    ]);

                    // DÉCRÉMENTER le stock de l'agent
                    $agentStock = AgentStock::where('user_id', $agentId)
                        ->where('product_id', $id)
                        ->first();

                    if ($agentStock->quantity < $item['quantity']) {
                        throw new \Exception("Stock insuffisant pour le produit: " . $item['name']);
                    }

                    $agentStock->decrement('quantity', $item['quantity']);
                }

                // C. Enregistrer le premier paiement (Encaissement réel)
                if ($amountPaid > 0) {
                    Payement::create([
                        'sale_id' => $sale->id,
                        'amount' => $amountPaid,
                        'payment_method' => $request->payment_method,
                        'recorded_by' => auth()->id(),
                    ]);
                }

                // D. Vider le panier session
                session()->forget('sale_cart');

                return response()->json([
                    'status' => true,
                    'message' => 'Vente enregistrée avec succès !',
                    'sale_id' => $sale->id
                ]);
            });

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }
}

}


