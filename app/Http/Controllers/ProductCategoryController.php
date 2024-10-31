<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;


class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $categories = ProductCategory::latest()->get();
            return DataTables::of($categories)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return '<div class="form-check font-16 mb-0">
                                <input class="form-check-input check-row" name="single-row" value="' . $row->id . '" type="checkbox" id="customerlist' . $row->id . '">
                                <label class="form-check-label" for="customerlist01">&nbsp;</label>
                            </div>';
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
                ->addColumn('action', function ($row) {
                    $edit_url = route('product-categories.edit', ['product_category' => $row->id]);
                    $delete_url = route('product-categories.destroy', ['product_category' => $row->id]);
                    $actionBtn = '
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item">
                            <a href="#" data-url="' . $edit_url . '" class="action-icon edit-btn"> <i
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
                ->rawColumns(['checkbox', 'status', 'action'])
                ->make(true);
        }
        return view('pages.product-categories.index');
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
            'name' => 'required|string|max:100',
            'id' => 'required|integer',
            'status' => 'sometimes|in:on,off'
        ]);
        $action = ProductCategory::updateOrCreate(
            ['id' => $request->id],
            ['name' => $request->name, 'status' => $request->status]
        );
        if ($action) {
            return response()->json([
                'status' => true,
                'message' => 'Action done successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'An error occured'
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $product_category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCategory $product_category)
    {
        if ($product_category) {
            return response()->json([
                'status' => true,
                'data' => $product_category,
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
    public function update(Request $request, ProductCategory $product_category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $product_category)
    {
        $deleted = $product_category->delete();
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
        $rows = ProductCategory::whereIn('id', $data)->delete();

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
