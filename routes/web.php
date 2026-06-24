<?php

use App\Http\Controllers\DependenciasController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TramitesController;
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

Route::middleware('auth')->controller(TramitesController::class)->group(function () {
    Route::get('/tramites', 'indexTramites')->name('indexTramites');
    Route::get('/tramites/agregar', 'agregarTramite')->name('agregarTramite');
    Route::post('/tramites/agregar', 'registrarTramite')->name('registrarTramite');
    Route::get('/tramites/activos', 'getTramitesActivos')->name('getTramitesActivos');
    Route::get('/tramites/inactivos', 'getTramitesInactivos')->name('getTramitesInactivos');
    Route::get('/tramites/editar/{tramite}', 'editarTramite')->name('editarTramite');
    Route::post('/tramites/editar/{tramite}', 'actualizarTramite')->name('actualizarTramite');
    Route::post('/tramites/deshabilitar/{tramite}', 'deshabilitarTramite')->name('deshabilitarTramite');
    Route::post('/tramites/habilitar/{tramite}', 'habilitarTramite')->name('habilitarTramite');
    Route::get('/tramites/requisitos/{tramite}', 'revisarRequisitos')->name('revisarRequisitos');
    Route::get('/tramites/requisitos/{tramite}/asignados', 'getRequisitosAsignados')->name('getRequisitosAsignados');
    Route::get('/tramites/requisitos/{tramite}/catalogo', 'getCatalogoDisponible')->name('getCatalogoDisponible');
    Route::post('/tramites/requisitos/{tramite}/asignar', 'asignarRequisitos')->name('asignarRequisitos');
    Route::post('/tramites/requisitos/{tramite}/quitar/{requisito}', 'quitarRequisito')->name('quitarRequisito');
});
