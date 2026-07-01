<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
<<<<<<< HEAD
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (! $request->user()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (! in_array($request->user()->role, $roles, true)) {
            return response()->json(['message' => 'Forbidden.'], 403);
=======
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles)) {
            return response()->json([
                'message' => 'Unauthorized. Role tidak sesuai.'
            ], 403);
>>>>>>> 22589e0065f85f8afe27c27718fc715915ec2569
        }

        return $next($request);
    }
}
