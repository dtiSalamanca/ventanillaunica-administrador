<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiOrdenesPagoController extends Controller
{
    //
    public function index(Request $request)
    {
        return response()->json([
            'message' => 'API de órdenes de pago',
            'data' => [
                // Aquí puedes agregar los datos que deseas devolver
            ],
        ]);
    }
}
