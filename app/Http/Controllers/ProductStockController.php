<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class ProductStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $stocks = ProductStock::latest()->get();

            return DataTables::of($stocks)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return '<div class="form-check font-16 mb-0">
                                <input class="form-check-input check-row" name="single-row" value="' . $row->id . '" type="checkbox" id="customerlist' . $row->id . '">
                                <label class="form-check-label" for="customerlist01">&nbsp;</label>
                            </div>';
                })
                ->addColumn('product', function ($row) {
                    $url = asset($row->product->photo);
                    $show_url = route('products.show', ['product' => $row->product_id]) . '?' . $row->slug;
                    $render = ' <div class="d-flex">
                                    <img src="' . $url . '" alt="table-user"
                                        class="me-3 rounded-circle avatar-sm">
                                    <div class="flex-1">
                                        <h5 class="mt-0 mb-1">
                                            <a href="' . $show_url . '" class="text-dark">
                                                ' . $row->name . '
                                            </a>
                                        </h5>
                                    <p class="mb-0 font-13">Category : ' . $row->product->product_category->name . ' </p>
                                    </div>
                                </div>';

                    return $render;
                })
                ->addColumn('operation', function ($row) {
                    $color = "";
                    $operation = $row->mouvement;
                    $operation_display = "";
                    if ($operation == 'e') {
                        $operation_display = "Input";
                        $color = "success";
                    } else if ($operation == 's') {
                        $operation_display = "output";
                        $color = "primary";
                    } else if ($operation == 'r') {
                        $operation_display = "Come back";
                        $color = "warning";
                    }

                    return '<span class="badge badge-soft-' . $color . '">' . $operation_display . '</span>';
                })
                ->addColumn('status', function ($row) {
                    $status = $row->status;
                    $color = 'success';
                    $status_display = 'Activated';
                    if ($status == 'off') {
                        $color = 'danger';
                        $status_display = 'Desactivated';
                    }
                    $render = ' <span class="badge badge-soft-' . $color . '">' . $status_display . '</span>';

                    return $render;
                })
                ->addColumn('price', function ($row) {
                    return '$ ' . $row->price;
                })
                ->addColumn('date', function ($row) {
                    return Carbon::parse($row->created_at)->locale('en_EN')->isoFormat('DD
                MMMM YYYY');
                })
                ->addColumn('author', function ($row) {
                    $url = asset('storage/users/' . $row->user->photo);
                    $render = ' <div class="d-flex">
                                    <img src="' . $url . '" alt="table-user"
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
                            <a href="' . $edit_url . '" class="action-icon edit-btn"> <i
                                    class="mdi mdi-square-edit-outline"></i></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#" data-url="' . $delete_url . '" class="action-icon delete-btn"> <i
                                    class="mdi mdi-delete"></i></a>
                        </li>
                    </ul>
                    ';

                    return $actionBtn;
                })
                ->rawColumns(['checkbox', 'product', 'operation', 'price', 'status', 'date', 'author', 'action'])
                ->make(true);
        }
        $products = Product::orderBy("name", "asc")->get();
        return view('pages.product-stock.index', compact("products"));
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
            'quantity' => 'required',
            'id' => 'required|integer'
        ]);

        $product = Product::find($request->product_id);
        if ($product) {
            $action = ProductStock::updateOrCreate(
                ['id' => $request->id],
                [
                    'mouvement' => 'e',
                    'quantity' => $request->quantity,
                    'price' => $product->price,
                    'status' => 'accepted'
                ]
            );
            $product->update(['quantity' => $action->quantity]);

            return response()->json([
                'status' => true,
                'message' => 'New stock of ' . $product->name . ' added!'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'product not found'
            ]);
        }
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
        //
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
