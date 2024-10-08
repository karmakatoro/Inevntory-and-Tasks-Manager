<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
