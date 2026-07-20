<?php

use App\Http\Controllers\SolicitudesController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->controller(SolicitudesController::class)->group(function () {
    Route::get('/solicitudes/{id}/detalles', 'verDetalles')->name('solicitudes.verDetalles');
    Route::post('/solicitudes/{id}/aprobar', 'aprobarSolicitud')->name('solicitudes.aprobar');
    Route::post('/solicitudes/{id}/rechazar', 'rechazarSolicitud')->name('solicitudes.rechazar');
    Route::get('/usuarios-ad/por-dependencia', 'getUsuariosPorDependencia')->name('usuariosAd.porDependencia');
    Route::get('/documento/ver', 'verDocumento')->name('documento.ver');
});
