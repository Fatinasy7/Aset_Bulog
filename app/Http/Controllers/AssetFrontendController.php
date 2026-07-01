<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssetFrontendController extends Controller
{
    public function index()
    {
        return view('admin.assets.index');
    }
}
