<?php

use App\Http\Controllers\Api\ApiOrdenesPagoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(ApiOrdenesPagoController::class)->group(function () {
    Route::get('/ordenes-pago', 'index');
});
