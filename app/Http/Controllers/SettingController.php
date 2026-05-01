<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
class SettingController extends Controller
{
    public function index()
    {
        return view('pages.settings.index');
    }
}
