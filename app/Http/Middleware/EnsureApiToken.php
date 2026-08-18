<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiToken
{
    /**
     * Verifica que la petición traiga el token compartido (X-API-Key).
     *
     * Solo los sistemas que conocen el secreto configurado en
     * config('services.sistema_pagos.api_token') pueden consumir estos endpoints.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expectedToken = config('services.sistema_pagos.api_token');
        $providedToken = $request->header('X-API-Key');

        if ($expectedToken === null || $providedToken === null || ! hash_equals($expectedToken, $providedToken)) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
