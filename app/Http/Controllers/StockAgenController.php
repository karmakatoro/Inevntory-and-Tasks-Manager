<?php


namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AgentStock;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class StockAgenController extends Controller
{
    //
    public function index(User $agent)
    {
         if(Auth::user()->type !=='admin'&& Auth::id() !== $agent->id ){
                abort(403,"Action non autorisée.");
            }
        if (request()->ajax()) {
            // On filtre par l'ID de l'agent reçu en paramètre


            $stock = AgentStock::with(['product.product_category'])
                ->where('user_id', $agent->id)
                ->latest()
                ->get();

            return DataTables::of($stock)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return '<div class="form-check font-16 mb-0">
                            <input class="form-check-input check-row" name="single-row" value="'.$row->id.'" type="checkbox" id="product'.$row->id.'">
                            <label class="form-check-label" for="product'.$row->id.'">&nbsp;</label>
                        </div>';
                })
                ->addColumn('product', function ($row) {
                    // Utilisation de la relation au singulier : $row->product
                    $photo = $row->product->photo ?? 'assets/images/products/default.png';
                    $name = $row->product->name ?? 'Produit inconnu';
                    $category = $row->product->product_category->name ?? 'N/A';

                    $url = asset($photo);
                    $show_url = route('products.show', ['product' => $row->product->id ?? 0]);

                    return '<div class="d-flex">
                            <img src="'.$url.'" alt="product-img" class="me-3 rounded-circle avatar-sm">
                            <div class="flex-1">
                                <h5 class="mt-0 mb-1">
                                    <a href="'.$show_url.'" class="text-dark">'.$name.'</a>
                                </h5>
                                <p class="mb-0 font-13">Category : '.$category.'</p>
                            </div>
                        </div>';
                })
                ->addColumn('price', function ($row) {
                    // Prix venant du modèle Product lié
                    $price = $row->product->price ?? 0;

                    return '$'.number_format($price, 2);
                })
                ->addColumn('quantity', function ($row) {
                    if ($row->quantity <= 0) {
                        return '<span class="badge bg-soft-danger text-danger">Rupture</span>';
                    } elseif ($row->quantity <= 5) {
                        return '<span class="badge bg-soft-warning text-warning">'.$row->quantity.' (Faible)</span>';
                    }

                    return '<span class="badge bg-soft-success text-success">'.$row->quantity.' en stock</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<ul class="list-inline mb-0">
                            <li class="list-inline-item">
                                <a href="javascript:void(0)" data-id="'.$row->id.'" class="action-icon btn-select">
                                    <i class="mdi mdi-check-circle text-success"></i> Sélectionner
                                </a>
                            </li>
                        </ul>';
                })
                ->rawColumns(['checkbox', 'product', 'price', 'quantity', 'action'])
                ->make(true);
        }

        // Très important : cette ligne doit être en dehors du bloc "if (request()->ajax())"
        // pour que la page s'affiche lors de la première visite (non-AJAX)
        return view('pages.stock-agent.index', compact('agent'));
    }
    }


