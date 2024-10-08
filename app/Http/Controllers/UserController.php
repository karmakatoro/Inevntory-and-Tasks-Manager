<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::latest()->get();

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return '<div class="form-check font-16 mb-0">
                                <input class="form-check-input" value="'.$row->id.'" type="checkbox" id="customerlist'.$row->id.'">
                                <label class="form-check-label" for="customerlist01">&nbsp;</label>
                            </div>';
                })
                ->addColumn('name', function ($row) {
                    $url = asset('storage/users/'.$row->photo);
                    $render = ' <div class="d-flex">
                                    <img src="'.$url.'" alt="table-user"
                                        class="me-3 rounded-circle avatar-sm">
                                    <div class="flex-1">
                                        <h5 class="mt-0 mb-1">
                                            <a href="javascript:void(0);" class="text-dark">
                                                '.$row->name.'
                                            </a>
                                        </h5>
                                        <p class="mb-0 font-13">Type : '.Str::ucfirst($row->type).'</p>
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

                ->addColumn('join', function ($row) {
                    return Carbon::parse($row->created_at)->locale('en_EN')->isoFormat('DD
                MMMM YYYY');
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item">
                            <a href="javascript:void(0);" data-id="'.$row->id.'" class="action-icon edit-bnt"> <i
                                    class="mdi mdi-square-edit-outline"></i></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="javascript:void(0);" data-id="'.$row->id.'" class="action-icon delete-btn"> <i
                                    class="mdi mdi-delete"></i></a>
                        </li>
                    </ul>
                    ';

                    return $actionBtn;
                })
                ->rawColumns(['checkbox', 'name', 'status', 'join', 'action'])
                ->make(true);
        }

        return view('pages.users.index');
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
            'phone' => 'required|max:50',
            'gender' => 'required|in:m,f',
            'type' => 'required|in:user,admin',
            'accred' => 'required|in:1,2,3',
            'status' => 'required|in:on,off',
        ]);

        $user = User::where('email', $request->email)->first();
        $check = User::find($request->id);
        if ($user && $check) {
            // update
            $update = $check->update($request->all());

            if ($update) {
                return response()->json([
                    'status' => true,
                    'message' => 'User '.$request->name.' updated successfully',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'An error occured',
                ]);
            }

        } elseif ($user && ! $check) {
            // user already exist
            return response()->json([
                'status' => false,
                'message' => 'User '.$request->name.' already exist',
            ]);
        } else {
            // create
            $data = $request->all();
            $photo = 'avatar-female.png';
            if ($request->gender == 'm') {
                $photo = 'avatar-male.png';
            }
            $password = Hash::make(1287635);
            $data['photo'] = $photo;
            $data['password'] = $password;
            $new = User::create($data);
            if ($new) {
                return response()->json([
                    'status' => true,
                    'message' => 'User '.$request->name.' created successfully',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'An error occured',
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
