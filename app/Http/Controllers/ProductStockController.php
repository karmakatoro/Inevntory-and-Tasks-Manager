<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProductStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function showProductOnstock()
    {
        if (request()->ajax()) {
            // Ajout de with() pour charger les catégories et éviter de ralentir le serveur
            $products = Product::with('product_category')->latest()->get();

            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return '<div class="form-check font-16 mb-0">
                            <input class="form-check-input check-row" name="single-row" value="'.$row->id.'" type="checkbox" id="product'.$row->id.'">
                            <label class="form-check-label" for="product'.$row->id.'">&nbsp;</label>
                        </div>';
                })
                ->addColumn('product', function ($row) {
                    $url = asset($row->photo);
                    $show_url = route('products.show', ['product' => $row->id]);

                    return ' <div class="d-flex">
                            <img src="'.$url.'" alt="product-img" class="me-3 rounded-circle avatar-sm">
                            <div class="flex-1">
                                <h5 class="mt-0 mb-1">
                                    <a href="'.$show_url.'" class="text-dark">'.$row->name.'</a>
                                </h5>
                                <p class="mb-0 font-13">Catégorie : '.($row->product_category->name ?? 'N/A').'</p>
                            </div>
                        </div>';
                })
                ->addColumn('price', function ($row) {
                    return '$'.number_format($row->price, 2);
                })
                ->addColumn('quantity', function ($row) {
                    // Correction logique : Danger si <= 0, Success si > 0
                    $color = $row->quantity <= 0 ? 'text-danger' : 'text-success';

                    return '<span class="'.$color.' fw-bold">'.$row->quantity.'</span>';
                })
                ->addColumn('status', function ($row) {
                    $color = $row->status == 'off' ? 'danger' : 'success';
                    $status_display = $row->status == 'off' ? 'Désactiver' : 'Actif';

                    return '<span class="badge badge-soft-'.$color.'">'.$status_display.'</span>';
                })
                ->addColumn('action', function ($row) {
                    $stock_url = route('products.show', $row->id);
                    $slug = route('products.show', $row->slug);

                    return '
                <ul class="list-inline mb-0">
                    <li class="list-inline-item">
                        <a href="javascript:void(0)"
                           data-id="'.$row->id.'"
                           data-name="'.addslashes($row->name).'"
                           data-price="'.$row->price.'"
                           class="action-icon btn btn-link btn-add-stock">
                           <i class="mdi mdi-archive-arrow-down text-primary"></i>
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a href="'.$slug.'"
                                data-id="'.$row->id.'"
                        class="action-icon btn-select">
                            <i class="mdi mdi-check-circle text-success"></i> Selectionner
                        </a>
                    </li>
                </ul>';
                })
                ->rawColumns(['checkbox', 'product', 'quantity', 'price', 'status', 'action'])
                ->make(true);
        }

        return view('pages.product-stock.stock');
    }

    public function index()
    {
        if (request()->ajax()) {
            $stocks = ProductStock::with('product.product_category',)
                ->latest()->get();

            return DataTables::of($stocks)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return '<div class="form-check font-16 mb-0">
                                <input class="form-check-input check-row" name="single-row" value="'.$row->id.'" type="checkbox" id="customerlist'.$row->id.'">
                                <label class="form-check-label" for="customerlist01">&nbsp;</label>
                            </div>';
                })
                ->addColumn('product', function ($row) {
                    // 1. Gestion de l'image (Basé sur ton test concluant)
                    $fileName = $row->product->photo ?? null;
                    $url = $fileName ? asset($fileName) : asset('assets/images/no-image.png');

                    // 2. Définition des variables nécessaires pour le rendu
                    $productName = $row->product->name ?? 'N/A';
                    $slug = $row->product->slug ?? '';
                    $categoryName = $row->product->product_category->name ?? 'N/A';

                    // 3. Construction de l'URL de redirection
                    $show_url = route('products.show', ['product' => $row->product_id]).'?'.$slug;

                    // 4. Rendu HTML
                    $render = ' <div class="d-flex align-items-center">
                    <img src="'.$url.'" alt="table-user"
                         class="me-3 rounded-circle avatar-sm" style="object-fit: cover;">
                    <div class="flex-1">
                        <h5 class="mt-0 mb-1">
                            <a href="'.$show_url.'" class="text-dark">
                                '.$productName.'
                            </a>
                        </h5>
                        <p class="mb-0 font-13">Catégorie : '.$categoryName.' </p>
                    </div>
                </div>';

                    return $render;
                })
                ->addColumn('operation', function ($row) {
                    $color = '';
                    $operation = $row->mouvement;
                    $operation_display = '';
                    if ($operation == 'e') {
                        $operation_display = 'Entrée';
                        $color = 'success';
                    } elseif ($operation == 's') {
                        $operation_display = 'Sortie';
                        $color = 'primary';
                    } elseif ($operation == 'r') {
                        $operation_display = 'Retour';
                        $color = 'warning';
                    }

                    return '<span class="badge badge-soft-'.$color.'">'.$operation_display.'</span>';
                })
                ->addColumn('status', function ($row) {
                    $status = $row->status;
                    $color = 'success';
                    $status_display = 'Actif';
                    if ($status == 'off') {
                        $color = 'danger';
                        $status_display = 'Désactiver';
                    }
                    $render = ' <span class="badge badge-soft-'.$color.'">'.$status_display.'</span>';

                    return $render;
                })
                ->addColumn('price', function ($row) {
                    return '$ '.$row->price;
                })
                ->addColumn('date', function ($row) {
                    return Carbon::parse($row->created_at)->locale('en_EN')->isoFormat('DD
                MMMM YYYY');
                })
                ->addColumn('author', function ($row) {
                    $url = asset('storage/users/'.$row->user->photo);
                    $render = ' <div class="d-flex">
                                    <img src="'.$url.'" alt="table-user"
                                        class="me-3 rounded-circle avatar-sm">
                                </div>';

                    return $render;
                })
                ->addColumn('action', function ($row) {
                    $edit_url = route('products-stock.edit', ['products_stock' => $row->id]);
                    $delete_url = route('products-stock.destroy', ['products_stock' => $row->id]);
                    $actionBtn = '
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item">
                            <a href="'.$edit_url.'" class="action-icon edit-btn"> <i
                                    class="mdi mdi-square-edit-outline"></i></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#" data-url="'.$delete_url.'" class="action-icon delete-btn"> <i
                                    class="mdi mdi-delete"></i></a>
                        </li>
                    </ul>
                    ';

                    return $actionBtn;
                })
                ->rawColumns(['checkbox', 'product', 'operation', 'price', 'status', 'date', 'author', 'action'])
                ->make(true);
        }
        $products = Product::orderBy('name', 'asc')->get();

        return view('pages.product-stock.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|numeric',
            'id' => 'required|integer',
        ]);

        $product = Product::findOrFail($request->product_id);
        if (! $product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not Found',
            ]);
        }
        if ($request->id > 0) {
            $oldMovement = ProductStock::findOrFail($request->id);
            if ($oldMovement) {
                $product->decrement('quantity', $oldMovement->quantity);
            }
        }
        $movement = ProductStock::updateOrCreate(
            ['id' => $request->id],
            [
                'mouvement' => 'e',
                'quantity' => $request->quantity,
                'price' => $product->price,
                'status' => 'accepted',

            ]
        );
        if (is_null($product->quantity)) {
            $product->quantity = 0;

        }
        $product->increment('quantity', $request->quantity);

        return response()->json([
            'status' => true,
            'message' => ($request->id > 0 ? 'Stock updated' : 'New stock added').' for '.$product->name,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductStock $productStock)
    {
        //

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductStock $products_stock)
    {
        if ($products_stock) {
            return response()->json([
                'status' => true,
                'data' => $products_stock,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'An error occured',
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductStock $productStock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductStock $products_stock)
    {

        $deleted = $products_stock->delete();
        if ($deleted) {
            return response()->json([
                'status' => true,
                'message' => 'Successful supression',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
            ]);
        }
    }

    public function delete_multiples(Request $request)
    {
        $data = $request->all_id;
        $rows = ProductStock::whereIn('id', $data)->delete();

        if ($rows) {
            return response()->json([
                'status' => true,
                'message' => 'Successful supressions',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'An error occured',
            ]);
        }
    }
}
