<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Views
    public function index()
    {
        return view('pages.auth.login');
    }

    public function forgot()
    {
        return view('pages.auth.forgot');
    }

    public function logout()
    {
        return view('pages.auth.logout');
    }
    // End Views

    // Logics
    public function get_connected(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'remember' => ['nullable'],
        ]);
        $remember = $request->has('remember') ? true : false;
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        } else {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $remember)) {
                return response()->json([
                    'status' => true,
                    'redirect' => route('dashboard'),
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'errors' => ['Identifiants Invalides'],
                ]);
            }
        }
    }

    public function get_logged_out()
    {
        Session::flush();
        Auth::logout();

        return redirect()->route('auth.logout');
    }
    // End Logics
}
