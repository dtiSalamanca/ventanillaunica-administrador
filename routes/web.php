<?php

use App\Http\Controllers\DependenciasController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes(['register' => false]);

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware('auth')->controller(DependenciasController::class)->group(function () {
    Route::get('/dependencias', 'indexDependencias')->name('indexDependencias');
    Route::get('/dependencias/agregar', 'agregarDependencia')->name('agregarDependencia');
    Route::post('/dependencias/agregar', 'registrarDependencia')->name('registrarDependencia');
    Route::get('/dependencias/activas', 'getDependenciasActivas')->name('getDependenciasActivas');
    Route::get('/dependencias/inactivas', 'getDependenciasInactivas')->name('getDependenciasInactivas');
    Route::get('/dependencias/editar/{dependencia}', 'editarDependencia')->name('editarDependencia');
    Route::post('/dependencias/editar/{dependencia}', 'actualizarDependencia')->name('actualizarDependencia');
    Route::post('/dependencias/deshabilitar/{dependencia}', 'deshabilitarDependencia')->name('deshabilitarDependencia');
    Route::post('/dependencias/habilitar/{dependencia}', 'habilitarDependencia')->name('habilitarDependencia');
});
