<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        return view('pages.dashboard.sales');
    }

    public function analytics()
    {
        return view('pages.dashboard.analytics');
    }
}
