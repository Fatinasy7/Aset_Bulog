<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CsrfProtectionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldPassThrough($request)) {
            return $next($request);
        }

        $token = $request->header('X-CSRF-TOKEN') ?: $request->input('_token');
        $sessionToken = null;

        try {
            if ($request->hasSession()) {
                $sessionToken = $request->session()->token();
            }
        } catch (\Throwable $exception) {
            $sessionToken = null;
        }

        if (! $token || ! $sessionToken || ! hash_equals($sessionToken, $token)) {
            return response()->json([
                'message' => 'CSRF token missing or invalid.'
            ], 419);
        }

        return $next($request);
    }

    protected function shouldPassThrough(Request $request): bool
    {
        if ($request->isMethodSafe()) {
            return true;
        }

        if ($request->hasHeader('Authorization')) {
            return true;
        }

        if ($request->is('api/auth/login') || $request->is('api/auth/register')) {
            return true;
        }

        return false;
    }
}
