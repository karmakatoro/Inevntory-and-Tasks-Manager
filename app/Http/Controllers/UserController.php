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
                                <input class="form-check-input check-row" name="single-row" value="'.$row->id.'" type="checkbox" id="customerlist'.$row->id.'">
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
                    $edit_url = route('users.edit', ['user' => $row->id]);
                    $delete_url = route('users.destroy', ['user' => $row->id]);
                    $actionBtn = '
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item">
                            <a href="#" data-id="'.$row->id.'" data-url="'.$edit_url.'" class="action-icon edit-btn"> <i
                                    class="mdi mdi-square-edit-outline"></i></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#" data-id="'.$row->id.'" data-url="'.$delete_url.'" class="action-icon delete-btn"> <i
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
        $check = User::find($request->id);
        $data = $request->all();
        if ($check) {
            $check_email = User::where('email', $request->email)->first();
            if ($check_email) {
                if ($check_email->id != $check->id) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Email already taken',
                    ]);
                }
            }
            $request->validate([
                'name' => 'sometimes|string|max:50',
                'email' => 'sometimes|email|max:50',
                'phone' => 'sometimes|max:50',
                'gender' => 'sometimes|in:m,f',
                'type' => 'sometimes|in:user,admin',
                'accred' => 'sometimes|in:1,2,3',
                'status' => 'sometimes|in:on,off',
            ]);

        } else {
            $request->validate([
                'name' => 'required|string|max:50',
                'email' => 'required|email|max:50|unique:users,email',
                'phone' => 'required|max:50',
                'gender' => 'required|in:m,f',
                'type' => 'required|in:user,admin',
                'accred' => 'required|in:1,2,3',
                'status' => 'required|in:on,off',
            ]);
            $photo = 'avatar-female.png';
            if ($request->gender == 'm') {
                $photo = 'avatar-male.png';
            }
            $password = Hash::make(1287635);
            $data['photo'] = $photo;
            $data['password'] = $password;

        }
        $action = User::updateOrCreate(
            ['id' => $request->id],
            $data
        );
        if ($action) {
            return response()->json([
                'status' => true,
                'message' => 'Action done successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'An error occured',
            ]);
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
        if ($user) {
            return response()->json([
                'status' => true,
                'data' => $user,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Une erreur est survenue',
            ]);
        }
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
        $id = $user->id;
        if ($id == auth()->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Impossible to perform this operation!',
            ]);
        }

        $deleted = $user->delete();
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

        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i] == auth()->user()->id) {
                unset($data[$i]);
            }
        }
        $rows = User::whereIn('id', $data)->delete();

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
