<?php

use App\Http\Controllers\AprobacionesController;
use App\Http\Controllers\DependenciasController;
use App\Http\Controllers\DocumentosPersonalesController;
use App\Http\Controllers\EnlaceController;
use App\Http\Controllers\PrediosController;
use App\Http\Controllers\RequisitosController;
use App\Http\Controllers\SolicitudesController;
use App\Http\Controllers\TramitesController;
use App\Http\Controllers\UsuariosController;
use Illuminate\Support\Facades\Route;

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

    // Prerequisitos (trámites requeridos)
    Route::get('/tramites/prerequisitos/{tramite}', 'revisarPrerequisitos')->name('revisarPrerequisitos');
    Route::get('/tramites/prerequisitos/{tramite}/asignados', 'getPrerequisitosAsignados')->name('getPrerequisitosAsignados');
    Route::get('/tramites/prerequisitos/{tramite}/catalogo', 'getPrerequisitosDisponibles')->name('getPrerequisitosDisponibles');
    Route::post('/tramites/prerequisitos/{tramite}/asignar', 'asignarPrerequisitos')->name('asignarPrerequisitos');
    Route::post('/tramites/prerequisitos/{tramite}/quitar/{requerido}', 'quitarPrerequisito')->name('quitarPrerequisito');
});

Route::middleware('auth')->controller(RequisitosController::class)->group(function () {
    Route::get('/requisitos', 'indexRequisitos')->name('indexRequisitos');
    Route::get('/requisitos/agregar', 'agregarRequisito')->name('agregarRequisito');
    Route::post('/requisitos/agregar', 'registrarRequisito')->name('registrarRequisito');
    Route::get('/requisitos/activos', 'getRequisitosActivos')->name('getRequisitosActivos');
    Route::get('/requisitos/inactivos', 'getRequisitosInactivos')->name('getRequisitosInactivos');
    Route::get('/requisitos/editar/{requisito}', 'editarRequisito')->name('editarRequisito');
    Route::post('/requisitos/editar/{requisito}', 'actualizarRequisito')->name('actualizarRequisito');
    Route::post('/requisitos/deshabilitar/{requisito}', 'deshabilitarRequisito')->name('deshabilitarRequisito');
    Route::post('/requisitos/habilitar/{requisito}', 'habilitarRequisito')->name('habilitarRequisito');
});

Route::middleware('auth')->controller(UsuariosController::class)->group(function () {
    Route::get('/usuarios', 'indexUsuarios')->name('indexUsuarios');
    Route::get('/usuarios/ad', 'getUsuariosAd')->name('getUsuariosAd');
    Route::post('/usuarios/asignar-dependencia', 'asignarDependencia')->name('asignarDependencia');
});

Route::middleware('auth')->controller(DocumentosPersonalesController::class)->group(function () {
    Route::get('/documentos/personales', 'indexDocumentosPersonales')->name('indexDocumentosPersonales');
    Route::get('/documentos/personales/agregar', 'agregarDocumentoPersonal')->name('agregarDocumentoPersonal');
    Route::post('/documentos/personales/agregar', 'registrarDocumentoPersonal')->name('registrarDocumentoPersonal');
    Route::get('/documentos/personales/activos', 'getDocumentosPersonalesActivos')->name('getDocumentosPersonalesActivos');
    Route::get('/documentos/personales/inactivos', 'getDocumentosPersonalesInactivos')->name('getDocumentosPersonalesInactivos');
    Route::get('/documentos/personales/editar/{documentoPersonal}', 'editarDocumentoPersonal')->name('editarDocumentoPersonal');
    Route::post('/documentos/personales/editar/{documentoPersonal}', 'actualizarDocumentoPersonal')->name('actualizarDocumentoPersonal');
    Route::post('/documentos/personales/deshabilitar/{documentoPersonal}', 'deshabilitarDocumentoPersonal')->name('deshabilitarDocumentoPersonal');
    Route::post('/documentos/personales/habilitar/{documentoPersonal}', 'habilitarDocumentoPersonal')->name('habilitarDocumentoPersonal');
});

Route::middleware('auth')->controller(PrediosController::class)->group(function () {
    Route::get('/documentos/predios', 'indexPredios')->name('indexPredios');
    Route::get('/documentos/predios/agregar', 'agregarDocumentoPredio')->name('agregarDocumentoPredio');
    Route::post('/documentos/predios/agregar', 'registrarDocumentoPredio')->name('registrarDocumentoPredio');
    Route::get('/documentos/predios/activos', 'getDocumentosPrediosActivos')->name('getDocumentosPrediosActivos');
    Route::get('/documentos/predios/inactivos', 'getDocumentosPrediosInactivos')->name('getDocumentosPrediosInactivos');
    Route::get('/documentos/predios/editar/{documentoPredio}', 'editarDocumentoPredio')->name('editarDocumentoPredio');
    Route::post('/documentos/predios/editar/{documentoPredio}', 'actualizarDocumentoPredio')->name('actualizarDocumentoPredio');
    Route::post('/documentos/predios/deshabilitar/{documentoPredio}', 'deshabilitarDocumentoPredio')->name('deshabilitarDocumentoPredio');
    Route::post('/documentos/predios/habilitar/{documentoPredio}', 'habilitarDocumentoPredio')->name('habilitarDocumentoPredio');
});

Route::middleware('auth')->controller(AprobacionesController::class)->group(function () {
    Route::get('/aprobaciones/documentos-personales', 'indexDocumentosPersonales')->name('indexAprobacionesDocumentosPersonales');
    Route::get('/aprobaciones/documentos-personales/buscar', 'buscarDocumentosPersonales')->name('buscarAprobacionesDocumentosPersonales');
    Route::get('/aprobaciones/documentos-personales/{documentoPersonal}/visualizar', 'visualizarDocumentoPersonal')->name('visualizarDocumentoPersonal');
    Route::post('/aprobaciones/documentos-personales/{documentoPersonal}/aprobar', 'aprobarDocumentoPersonal')->name('aprobarDocumentoPersonal');
    Route::post('/aprobaciones/documentos-personales/{documentoPersonal}/rechazar', 'rechazarDocumentoPersonal')->name('rechazarDocumentoPersonal');

    Route::get('/aprobaciones/predios', 'indexPredios')->name('indexAprobacionesPredios');
    Route::get('/aprobaciones/predios/buscar', 'buscarPredios')->name('buscarAprobacionesPredios');
    Route::post('/aprobaciones/predios/{predio}/aprobar', 'aprobarPredio')->name('aprobarPredio');
    Route::post('/aprobaciones/predios/{predio}/rechazar', 'rechazarPredio')->name('rechazarPredio');
    Route::get('/aprobaciones/documentos-predios/{documentoPredio}/visualizar', 'visualizarDocumentoPredio')->name('visualizarDocumentoPredio');
    Route::post('/aprobaciones/documentos-predios/{documentoPredio}/aprobar', 'aprobarDocumentoPredio')->name('aprobarDocumentoPredio');
    Route::post('/aprobaciones/documentos-predios/{documentoPredio}/rechazar', 'rechazarDocumentoPredio')->name('rechazarDocumentoPredio');
});

// =========================================================================
// AJAX - Solicitudes con información de turnado (para el admin)
// =========================================================================
Route::middleware('auth')->get('/ajax/solicitudes-completas', [SolicitudesController::class, 'getSolicitudesCompletas'])
    ->name('ajax.solicitudes.completas');

// =========================================================================
// Rutas para rol Enlace
// =========================================================================
Route::middleware('auth')->prefix('enlace')->name('enlace.')->controller(EnlaceController::class)->group(function () {
    Route::get('/home', 'home')->name('home');
    Route::get('/tramites-turnados', 'tramitesTurnados')->name('tramitesTurnados');
    Route::get('/tramites-turnados/data', 'getTramitesTurnados')->name('getTramitesTurnados');
    Route::get('/tramites-turnados/{id}/detalles', 'verDetalles')->name('tramitesTurnadosDetalles');
    Route::post('/tramites-turnados/{id}/aprobar', 'aprobarSolicitud')->name('tramitesTurnadosAprobar');
    Route::post('/tramites-turnados/{id}/rechazar', 'rechazarSolicitud')->name('tramitesTurnadosRechazar');
});
