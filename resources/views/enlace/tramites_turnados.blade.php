@extends('layouts.enlace')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/predios/indexPredios.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap5.min.css">
@endsection

@section('content')
    <div class="main-container">

        {{-- Header --}}
        <div class="page-header">
            <div class="header-content">
                <img src="{{ asset('images/escudoBlanco.png') }}" alt="Escudo de Salamanca" class="header-escudo">

                <div class="header-main">
                    <h1 class="page-title">Trámites Turnados</h1>
                    <p class="page-subtitle">Solicitudes de trámites turnadas para su atención</p>
                </div>
            </div>
        </div>

        {{-- Card principal --}}
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs mb-3" id="tabs-turnados" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pendientes-tab" data-bs-toggle="tab"
                            data-bs-target="#pendientes" type="button" role="tab" aria-controls="pendientes"
                            aria-selected="true">
                            <i class="fa-solid fa-clock me-1"></i> Pendientes
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="atendidas-tab" data-bs-toggle="tab" data-bs-target="#atendidas"
                            type="button" role="tab" aria-controls="atendidas" aria-selected="false">
                            <i class="fa-solid fa-check-circle me-1"></i> Atendidas
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="rechazadas-tab" data-bs-toggle="tab" data-bs-target="#rechazadas"
                            type="button" role="tab" aria-controls="rechazadas" aria-selected="false">
                            <i class="fa-solid fa-ban me-1"></i> Rechazadas
                        </button>
                    </li>
                </ul>
                <div class="tab-content" id="tabs-turnados-content">
                    <div class="tab-pane fade show active" id="pendientes" role="tabpanel" aria-labelledby="pendientes-tab">
                        <div class="table-container">
                            <div class="table-responsive">
                                <table id="tabla-turnados-pendientes" class="table table-striped align-middle"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="w-documento"><i class="fas fa-hashtag me-2"></i>Solicitud</th>
                                            <th class="w-tramite"><i class="fas fa-file-lines me-2"></i>Trámite</th>
                                            <th class="w-descripcion"><i
                                                    class="fas fa-building-columns me-2"></i>Dependencia</th>
                                            <th class="w-usuario"><i class="fas fa-user me-2"></i>Ciudadano</th>
                                            <th class="w-fecha"><i class="fas fa-calendar me-2"></i>Fecha de turnado</th>
                                            <th class="w-estado"><i class="fas fa-flag me-2"></i>Estatus</th>
                                            <th class="w-acciones"><i class="fas fa-cogs me-2"></i>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Datos cargados por AJAX --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="atendidas" role="tabpanel" aria-labelledby="atendidas-tab">
                        <div class="table-container">
                            <div class="table-responsive">
                                <table id="tabla-turnados-atendidas" class="table table-striped align-middle"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="w-documento"><i class="fas fa-hashtag me-2"></i>Solicitud</th>
                                            <th class="w-tramite"><i class="fas fa-file-lines me-2"></i>Trámite</th>
                                            <th class="w-descripcion"><i
                                                    class="fas fa-building-columns me-2"></i>Dependencia</th>
                                            <th class="w-usuario"><i class="fas fa-user me-2"></i>Ciudadano</th>
                                            <th class="w-fecha"><i class="fas fa-calendar me-2"></i>Fecha de turnado</th>
                                            <th class="w-estado"><i class="fas fa-flag me-2"></i>Estatus</th>
                                            <th class="w-acciones"><i class="fas fa-cogs me-2"></i>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Datos cargados por AJAX --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="rechazadas" role="tabpanel" aria-labelledby="rechazadas-tab">
                        <div class="table-container">
                            <div class="table-responsive">
                                <table id="tabla-turnados-rechazadas" class="table table-striped align-middle"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="w-documento"><i class="fas fa-hashtag me-2"></i>Solicitud</th>
                                            <th class="w-tramite"><i class="fas fa-file-lines me-2"></i>Trámite</th>
                                            <th class="w-descripcion"><i
                                                    class="fas fa-building-columns me-2"></i>Dependencia</th>
                                            <th class="w-usuario"><i class="fas fa-user me-2"></i>Ciudadano</th>
                                            <th class="w-fecha"><i class="fas fa-calendar me-2"></i>Fecha de turnado</th>
                                            <th class="w-estado"><i class="fas fa-flag me-2"></i>Estatus</th>
                                            <th class="w-acciones"><i class="fas fa-cogs me-2"></i>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Datos cargados por AJAX --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/responsive.bootstrap5.min.js"></script>
    <script>
        window.enlaceRoutes = {
            tramitesTurnados: "{{ route('enlace.getTramitesTurnados') }}",
            tramitesTurnadosDetalles: "{{ route('enlace.tramitesTurnadosDetalles', ['id' => '__ID__']) }}",
        };
    </script>
    <script src="{{ asset('js/enlace/tramites_turnados.js') }}"></script>
@endsection
