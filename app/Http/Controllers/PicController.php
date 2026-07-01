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
                'nama' => $pic->name,
                'jabatan' => $this->mapRole($pic->role),
                'email' => $pic->email,
                'telepon' => $pic->telepon,
                'createdAt' => $pic->created_at?->toISOString(),
                'updatedAt' => $pic->updated_at?->toISOString(),
            ];
        })->values();

        return response()->json($mappedPics);
    }

    protected function mapRole(string $role): string
    {
        return match (strtolower($role)) {
            'admin_it', 'admin' => 'Admin',
            'user_pic', 'pic' => 'PIC',
            'manajemen', 'manager' => 'Manajemen',
            default => $role,
        };
    }

    protected function normalizeRole(string $role): string
    {
        return match (strtolower($role)) {
            'admin', 'admin_it' => 'admin_it',
            'pic', 'user_pic' => 'user_pic',
            'manajemen', 'manager' => 'manajemen',
            default => 'user_pic',
        };
    }

    protected function mapPic(User $user): array
    {
        return [
            'id' => $user->id,
            'nama' => $user->name,
            'jabatan' => $this->mapRole($user->role),
            'email' => $user->email,
            'telepon' => $user->telepon,
            'createdAt' => $user->created_at?->toISOString(),
            'updatedAt' => $user->updated_at?->toISOString(),
        ];
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:6'],
            'jabatan' => ['required', 'string', 'max:50'],
            'telepon' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name' => $validated['nama'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password'] ?? 'Password123!'),
            'role' => $this->normalizeRole($validated['jabatan']),
            'telepon' => $validated['telepon'] ?? null,
        ]);

        return response()->json($this->mapPic($user), 201);
    }

    public function update(Request $request, User $pic)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $pic->id],
            'password' => ['nullable', 'string', 'min:6'],
            'jabatan' => ['required', 'string', 'max:50'],
            'telepon' => ['nullable', 'string', 'max:20'],
        ]);

        $data = [
            'name' => $validated['nama'],
            'email' => strtolower($validated['email']),
            'role' => $this->normalizeRole($validated['jabatan']),
            'telepon' => $validated['telepon'] ?? $pic->telepon,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $pic->fill($data);
        $pic->save();

        return response()->json($this->mapPic($pic));
    }

    public function destroy(User $pic)
    {
        $pic->delete();

        return response()->json(['message' => 'PIC deleted successfully.']);
    }
}
