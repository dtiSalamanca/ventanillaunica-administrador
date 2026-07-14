<?php
use App\Http\Controllers\AjaxEmmaController;
use App\Http\Controllers\PrediosEmmaController;
use App\Http\Controllers\SolicitudesEmmaController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->controller(PrediosEmmaController::class)->group(function () {
    Route::get('/aprobacion-predios/{id}', 'validar')->name('predio.validar');
});

Route::middleware('auth')->controller(SolicitudesEmmaController::class)->group(function () {
    Route::get('/solicitudes', 'index')->name('solicitudes.index');
});

Route::middleware('auth')->controller(AjaxEmmaController::class)->group(function () {
    Route::get('/ajax/solicitudes', 'solicitudes')->name('ajax.solicitudes');
});