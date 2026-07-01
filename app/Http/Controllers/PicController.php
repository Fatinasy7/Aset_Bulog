<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PicController extends Controller
{
    public function index()
    {
        $pics = User::whereIn('role', ['user_pic', 'admin_it', 'manajemen'])
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role', 'telepon', 'created_at', 'updated_at']);

        $mappedPics = $pics->map(function ($pic) {
            return [
                'id' => $pic->id,
                'name' => $pic->name,
                'email' => $pic->email,
                'role' => $pic->role,
                'telepon' => $pic->telepon,
            ];
        });

        return response()->json($mappedPics);
    }

    // Pastikan Anda juga sudah memiliki fungsi store, update, dan destroy di bawah sini
}