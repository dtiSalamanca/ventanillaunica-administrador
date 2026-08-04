<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class OrdesPagoController extends Controller
{
    public function consultaCuentasCri(Request $request): Response
    {
        $url = 'http://172.17.5.214/sisalamanca/public/api/consultaCuentasCri';

        try {
            $response = Http::timeout(15)
                ->connectTimeout(5)
                ->get($url, $request->query());
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo contactar el servicio interno.',
                'error' => $exception->getMessage(),
            ], 502);
        }

        $contentType = $response->header('Content-Type') ?: 'application/json';

        if ($response->successful() && str_contains($contentType, 'application/json')) {
            return response()->json($response->json(), $response->status());
        }

        return response($response->body(), $response->status(), [
            'Content-Type' => $contentType,
        ]);
    }
}
