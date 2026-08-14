<?php

use App\Http\Controllers\Api\ApiOrdenesPagoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(ApiOrdenesPagoController::class)->group(function () {
    // Endpoint 1: lista de órdenes (ciudadano, dependencia, monto)
    Route::get('/ordenes-pago', 'index');
    // Endpoint 2: detalle de una orden específica (descripción y CRI)
    Route::get('/ordenes-pago/{id_orden_pago}', 'show')->whereNumber('id_orden_pago');
    // Endpoint 3: guardar el pago (asigna folio a la orden)
    Route::post('/ordenes-pago/{id_orden_pago}/folio', 'aplicarFolio');
});
