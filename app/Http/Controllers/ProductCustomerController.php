<?php

namespace App\Http\Controllers;

use App\Models\ProductCustomer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProductCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $customers = ProductCustomer::latest()->get();

            return DataTables::of($customers)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return '<div class="form-check font-16 mb-0">
                                <input class="form-check-input check-row" name="single-row" value="' . $row->id . '" type="checkbox" id="customerlist' . $row->id . '">
                                <label class="form-check-label" for="customerlist01">&nbsp;</label>
                            </div>';
                })
                ->addColumn('name', function ($row) {
                    $url = asset('storage/users/avatar-female.png');
                    $gender = "Female";
                    if ($row->gender == 'm') {
                        $url = asset('storage/users/avatar-male.png');
                        $gender = "Male";
                    }
                    $render = ' <div class="d-flex">
                                    <img src="' . $url . '" alt="table-user"
                                        class="me-3 rounded-circle avatar-sm">
                                    <div class="flex-1">
                                        <h5 class="mt-0 mb-1">
                                            <a href="javascript:void(0);" class="text-dark">
                                                ' . $row->name . '
                                            </a>
                                        </h5>
                                        <p class="mb-0 font-13">Gender : ' . $gender . '</p>
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

                ->addColumn('join', function ($row) {
                    return Carbon::parse($row->created_at)->locale('en_EN')->isoFormat('DD
                MMMM YYYY');
                })
                ->addColumn('contacts', function ($row) {
                    $render = '  <h6 class="mt-0 mb-1">Email :
                                            <a href="mailto:' . $row->email . '" class="text-dark">
                                                ' . $row->email . '
                                            </a>
                                        </h6>
                                        <h6 class="mt-0 mb-1">Phone :
                                            <a href="tel:' . $row->phone . '" class="text-dark">
                                                ' . $row->phone . '
                                            </a>
                                        </h6>';
                    return $render;
                })
                ->addColumn('action', function ($row) {
                    $edit_url = route('customers.edit', ['customer' => $row->id]);
                    $delete_url = route('customers.destroy', ['customer' => $row->id]);
                    $actionBtn = '
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item">
                            <a href="#" data-id="' . $row->id . '" data-url="' . $edit_url . '" class="action-icon edit-btn"> <i
                                    class="mdi mdi-square-edit-outline"></i></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#" data-id="' . $row->id . '" data-url="' . $delete_url . '" class="action-icon delete-btn"> <i
                                    class="mdi mdi-delete"></i></a>
                        </li>
                    </ul>
                    ';

                    return $actionBtn;
                })
                ->rawColumns(['checkbox', 'name', 'status', 'contacts', 'join', 'action'])
                ->make(true);
        }

        return view('pages.customers.index');
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
            'name' => 'required|string|max:50',
            'email' => 'required|email|max:50',
            'address' => 'required|string|max:50',
            'phone' => 'required|max:50',
            'gender' => 'required|in:m,f',
            'status' => 'required|in:on,off',
            'id' => 'required|integer'
        ]);

        $action = ProductCustomer::updateOrCreate(
            ['id' => $request->id],
            $request->all()
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
    public function show(ProductCustomer $productCustomer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCustomer $customer)
    {
        if ($customer) {
            return response()->json([
                'status' => true,
                'data' => $customer,
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
    public function update(Request $request, ProductCustomer $productCustomer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCustomer $customer)
    {
        $deleted = $customer->delete();
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
        $rows = ProductCustomer::whereIn('id', $data)->delete();

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
