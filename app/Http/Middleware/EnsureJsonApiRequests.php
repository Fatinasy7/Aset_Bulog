<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureJsonApiRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('patch')) {
            $contentType = $request->headers->get('content-type', '');
            if ($contentType !== '' && stripos($contentType, 'application/json') === false) {
                return response()->json([
                    'message' => 'Content-Type harus application/json.'
                ], 415);
            }
        }

        return $next($request);
    }
}
