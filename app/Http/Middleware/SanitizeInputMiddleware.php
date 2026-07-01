<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeInputMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();
        $sanitized = $this->sanitize($input);
        $request->replace($sanitized);

        return $next($request);
    }

    protected function sanitize(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sanitize($value);
                continue;
            }

            if (is_string($value)) {
                $data[$key] = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $value);
                $data[$key] = strip_tags($data[$key]);
                $data[$key] = preg_replace('/\s+/', ' ', trim($data[$key]));
            }
        }

        return $data;
    }
}
