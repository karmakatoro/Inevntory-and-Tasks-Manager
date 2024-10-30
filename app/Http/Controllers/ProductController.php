<?php

namespace App\Http\Controllers;


use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
                                <input class="form-check-input check-row" name="single-row" value="' . $row->id . '" type="checkbox" id="customerlist' . $row->id . '">
                                <label class="form-check-label" for="customerlist01">&nbsp;</label>
                            </div>';
                })
                ->addColumn('product', function ($row) {
                    $url = asset($row->photo);
                    $render = ' <div class="d-flex">
                                    <img src="' . $url . '" alt="table-user"
                                        class="me-3 rounded-circle avatar-sm">
                                    <div class="flex-1">
                                        <h5 class="mt-0 mb-1">
                                            <a href="javascript:void(0);" class="text-dark">
                                                ' . $row->name . '
                                            </a>
                                        </h5>
                                    <p class="mb-0 font-13">Category :' . $row->product_category->name . ' </p>
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
                    $render = ' <span class="badge badge-soft-' . $color . '">' . $status_display . '</span>';

                    return $render;
                })
                ->addColumn('date', function ($row) {
                    return Carbon::parse($row->created_at)->locale('en_EN')->isoFormat('DD
                MMMM YYYY');
                })
                ->addColumn('price', function ($row) {
                    return '$ ' . $row->price;
                })
                ->addColumn('action', function ($row) {
                    $edit_url = route('products.edit', ['product' => $row->id]);
                    $delete_url = route('products.destroy', ['product' => $row->id]);
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
        return view('pages.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:products,name',
            'product_category_id' => 'required|integer|exists:product_categories,id',
            'description' => 'required|string|max:500',
            'photo' => 'required|image|mimes:png,jpg,jpeg',
            'status' => 'sometimes|in:on,off',
            'price' => 'required',
            'files' => 'sometimes|array',
            'files.*' => 'sometimes|image|mimes:png,jpg,jpeg',
        ]);
        $poster = '';
        $gallery = '';
        if ($request->hasFile('photo')) {
            $poster = $this->functions->store_file($request->photo, 'products/');
        }
        if ($request->hasFile('files')) {
            $gallery = $this->functions->store_multiples_file($request->file('files'), 'products/gallery');
        }
        $data = [
            'slug' => Str::slug($request->name),
            'subcategories' => json_encode($request->subcategories),
            'name' => $request->name,
            'description' => $request->description,
            'photo' => $poster,
            'gallery' => $gallery,
            'price' => $request->price,
            'status' => $request->status
        ];
        $create = Product::create($data);
        if ($create) {
            return redirect()->route('products.index')->with('success', 'Product created successfully');
        } else {
            return redirect()->back()->with('error', 'An error occured while creating product')->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
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
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'sometimes|string|max:100',
            'product_category_id' => 'sometimes|integer|exists:product_categories,id',
            'description' => 'sometimes|string|max:500',
            'photo' => 'sometimes|image|mimes:png,jpg,jpeg',
            'status' => 'sometimes|in:on,off',
            'price' => 'sometimes',
        ]);
        $name = '';
        $subcategories = '';
        $poster = $product->photo;
        if (!$request->name) {
            $name = $product->name;
        }
        if (!$request->subcategories) {
            $subcategories = $product->subcategories;
        }
        if ($request->hasFile('photo')) {
            $poster = $this->functions->store_file($request->photo, 'products/');
        }
        $data = [
            'slug' => Str::slug($name),
            'subcategories' => json_encode($subcategories),
            'name' => $request->name,
            'description' => $request->description,
            'photo' => $poster,
            'price' => $request->price,
            'status' => $request->status
        ];
        $update = $product->update($data);
        if ($update) {
            return redirect()->route('products.index')->with('success', 'Product updated successfully');
        } else {
            return redirect()->back()->with('error', 'An error occured while creating product')->withInput();
        }
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
