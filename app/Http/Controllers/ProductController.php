<?php

namespace App\Http\Controllers;

use App\Http\Requests\FromRequestProduct;
use App\Models\Product;
use App\Models\ProductCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProductController extends Controller
{
    private $functions;

    public function __construct()
    {
        $this->functions = new FunctionsController;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $products = Product::latest()->get();

            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return '<div class="form-check font-16 mb-0">
                                <input class="form-check-input check-row" name="single-row" value="'.$row->id.'" type="checkbox" id="customerlist'.$row->id.'">
                                <label class="form-check-label" for="customerlist01">&nbsp;</label>
                            </div>';
                })
                ->addColumn('product', function ($row) {
                    $url = asset($row->photo);
                    $show_url = route('products.show', ['product' => $row->id]).'?'.$row->slug;
                    $render = ' <div class="d-flex">
                                    <img src="'.$url.'" alt="table-user"
                                        class="me-3 rounded-circle avatar-sm">
                                    <div class="flex-1">
                                        <h5 class="mt-0 mb-1">
                                            <a href="'.$show_url.'" class="text-dark">
                                                '.$row->name.'
                                            </a>
                                        </h5>
                                    <p class="mb-0 font-13">Category : '.$row->product_category->name.' </p>
                                    </div>
                                </div>';

                    return $render;
                })
                ->addColumn('status', function ($row) {
                    $status = $row->status;
                    $color = 'success';
                    $status_display = 'Activated';
                    if ($status == 'off') {
                        $color = 'danger';
                        $status_display = 'Desactivated';
                    }
                    $render = ' <span class="badge badge-soft-'.$color.'">'.$status_display.'</span>';

                    return $render;
                })
                ->addColumn('date', function ($row) {
                    return Carbon::parse($row->created_at)->locale('en_EN')->isoFormat('DD
                MMMM YYYY');
                })
                ->addColumn('price', function ($row) {
                    return '$ '.$row->price;
                })
                ->addColumn('action', function ($row) {
                    $show_url = route('products.show', ['product' => $row->id]).'?'.$row->slug;
                    $edit_url = route('products.edit', ['product' => $row->id]);
                    $delete_url = route('products.destroy', ['product' => $row->id]);
                    $urlSlug = route('products.show', $row->slug);
                    $actionBtn = '
    <ul class="list-inline mb-0">
        <li class="list-inline-item">
            <a href="'.$urlSlug.'"
               data-id="'.$row->id.'"
               data-name="'.addslashes($row->name).'"
               data-price="'.addslashes($row->price).'"
               class="action-icon btn btn-link  btn-add-stock">
               <i class="mdi mdi-archive-arrow-down"></i>
            </a>
        </li>
        <li class="list-inline-item">
            <a href="'.$show_url.'" class="action-icon"> <i class="mdi mdi-eye"></i></a>
        </li>
        <li class="list-inline-item">
            <a href="'.$edit_url.'" class="action-icon"> <i class="mdi mdi-square-edit-outline"></i></a>
        </li>
        <li class="list-inline-item">
            <a href="#" data-url="'.$delete_url.'" class="action-icon delete-btn"> <i class="mdi mdi-delete"></i></a>
        </li>
    </ul>';

                    return $actionBtn;
                })
                ->rawColumns(['checkbox', 'product', 'status', 'date', 'price', 'action'])
                ->make(true);
        }

        return view('pages.products.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ProductCategory::orderBy('name', 'asc')->get();
        $modelProduct = new Product;

        return view('pages.products.create', compact('categories', 'modelProduct'));
    }

    public function product_price(Request $request)
    {

        $request->validate([
            'id' => 'required|integer|exists:products,id',
        ]);
        $product = Product::find($request->id);
        if ($product) {
            return response()->json([
                'status' => true,
                'price' => $product->price,
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FromRequestProduct $request)
    {
        $data = $request->validated();

        // On utilise les variables pour remplir le tableau $data
        if ($request->hasFile('photo')) {
            $data['photo'] = $this->functions->store_file($request->photo, 'products');
        }

        if ($request->hasFile('files')) {
            $data['gallery'] = $this->functions->store_multiples_file($request->file('files'), 'products/gallery');
        }

        // Gestion propre des sous-catégories (en JSON pour ta migration string)
        $data['subcategories'] = json_encode($request->subcategories ?? []);
        $data['status'] = $request->status ?? 'off';

        $new = Product::create($data); // Ici, $data contient maintenant les bons chemins !

        if ($new) {
            return redirect()->route('products.index')->with('success', 'Product created successfully');
        }

        return redirect()->back()->with('error', 'An error occurred')->withInput();
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('pages.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = ProductCategory::orderBy('name', 'asc')->get();

        return view('pages.products.edit', compact('categories', 'product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FromRequestProduct $request, Product $product)
    {

        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->functions->store_file($request->photo, 'products');
        } else {
            $data['photo'] = $product->photo;
        }

        $data['subcategories'] = json_encode($request->subcategories ?? []);

        if ($product->update($data)) {
            return redirect()->route('products.index')->with('success', 'Product updated successfully');
        }

        return redirect()->back()->with('error', 'Update failed')->withInput();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $deleted = $product->delete();
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
        $rows = Product::whereIn('id', $data)->delete();

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
