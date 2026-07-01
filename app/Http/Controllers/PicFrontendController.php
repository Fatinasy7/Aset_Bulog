<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PicFrontendController extends Controller
{
    public function index()
    {
        // Mengarahkan ke file view di resources/views/admin/pics/index.blade.php
        return view('admin.pics.index');
    }
}