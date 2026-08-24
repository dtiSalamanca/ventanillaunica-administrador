@extends('layouts.admin')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/reporteador/indexReporteador.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap5.min.css">

    <div class="main-container">
        {{-- Header --}}
        <div class="page-header">
            <div class="header-content">
                <img src="{{ asset('images/escudoBlanco.png') }}" alt="Escudo de Salamanca" class="header-escudo">

                <div class="header-main">
                    <h1 class="page-title">Reporteador</h1>
                    <p class="page-subtitle">Reportes y estadísticas de la Ventanilla Única</p>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="card reporteador-filtros">
            <div class="reporteador-filtros-header">
                <h2 class="reporteador-table-title">
                    <i class="fas fa-filter me-2"></i>Filtros del reporte
                </h2>
                <p class="reporteador-table-sub">Selecciona los criterios y genera el reporte en Excel</p>
            </div>
            <div class="reporteador-filtros-body">
                <form id="form-reporteador-filtros" method="GET" action="{{ route('reporteador.excel') }}"
                    autocomplete="off">
                    <div class="reporteador-filtros-grid">
                        <div class="filtro-group">
                            <label for="filtro-dependencia"><i class="fas fa-building me-1"></i>Dependencia</label>
                            <select id="filtro-dependencia" name="fk_dependencia" class="filtro-control">
                                <option value="">Todas</option>
                                @foreach ($dependencias as $dependencia)
                                    <option value="{{ $dependencia->id_dependencia }}">
                                        {{ $dependencia->nombre_dependencia }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filtro-group">
                            <label for="filtro-estatus"><i class="fas fa-flag me-1"></i>Estado</label>
                            <select id="filtro-estatus" name="estatus" class="filtro-control">
                                <option value="">Todos</option>
                                <option value="0">Pendiente</option>
                                <option value="1">Turnada</option>
                                <option value="2">Rechazada</option>
                                <option value="3">Por pagar</option>
                                <option value="4">Completado</option>
                            </select>
                        </div>

                        <div class="filtro-group">
                            <label for="filtro-fecha-desde"><i class="fas fa-calendar me-1"></i>Desde</label>
                            <input type="date" id="filtro-fecha-desde" name="fecha_desde" class="filtro-control">
                        </div>

                        <div class="filtro-group">
                            <label for="filtro-fecha-hasta"><i class="fas fa-calendar me-1"></i>Hasta</label>
                            <input type="date" id="filtro-fecha-hasta" name="fecha_hasta" class="filtro-control">
                        </div>
                    </div>

                    <div class="reporteador-filtros-actions">
                        <button type="button" id="btn-aplicar-filtros" class="btn-filtro">
                            <i class="fas fa-filter me-1"></i>Aplicar filtros
                        </button>
                        <button type="submit" class="btn-excel">
                            <i class="fas fa-file-excel me-1"></i>Generar reporte Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Indicadores --}}
        <div class="reporteador-grid">
            <div class="reporteador-card">
                <div class="reporteador-card-icon reporteador-card-icon--primary">
                    <i class="fas fa-file-circle-check"></i>
                </div>
                <div class="reporteador-card-info">
                    <span class="reporteador-card-valor">{{ $totalSolicitudes }}</span>
                    <span class="reporteador-card-etiqueta">Solicitudes totales</span>
                </div>
            </div>

            <div class="reporteador-card">
                <div class="reporteador-card-icon reporteador-card-icon--warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="reporteador-card-info">
                    <span class="reporteador-card-valor">{{ $solicitudesPendientes }}</span>
                    <span class="reporteador-card-etiqueta">Pendientes</span>
                </div>
            </div>

            <div class="reporteador-card">
                <div class="reporteador-card-icon reporteador-card-icon--success">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div class="reporteador-card-info">
                    <span class="reporteador-card-valor">{{ $solicitudesTurnadas }}</span>
                    <span class="reporteador-card-etiqueta">Turnadas</span>
                </div>
            </div>

            <div class="reporteador-card">
                <div class="reporteador-card-icon reporteador-card-icon--cyan">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="reporteador-card-info">
                    <span class="reporteador-card-valor">{{ $solicitudesPorPagar }}</span>
                    <span class="reporteador-card-etiqueta">Por pagar</span>
                </div>
            </div>

            <div class="reporteador-card">
                <div class="reporteador-card-icon reporteador-card-icon--violet">
                    <i class="fas fa-check-double"></i>
                </div>
                <div class="reporteador-card-info">
                    <span class="reporteador-card-valor">{{ $solicitudesCompletadas }}</span>
                    <span class="reporteador-card-etiqueta">Completadas</span>
                </div>
            </div>

            <div class="reporteador-card">
                <div class="reporteador-card-icon reporteador-card-icon--danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="reporteador-card-info">
                    <span class="reporteador-card-valor">{{ $solicitudesRechazadas }}</span>
                    <span class="reporteador-card-etiqueta">Rechazadas</span>
                </div>
            </div>

            <div class="reporteador-card">
                <div class="reporteador-card-icon reporteador-card-icon--orange">
                    <i class="fas fa-file-lines"></i>
                </div>
                <div class="reporteador-card-info">
                    <span class="reporteador-card-valor">{{ $totalTramites }}</span>
                    <span class="reporteador-card-etiqueta">Trámites</span>
                </div>
            </div>

            <div class="reporteador-card">
                <div class="reporteador-card-icon reporteador-card-icon--secondary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="reporteador-card-info">
                    <span class="reporteador-card-valor">{{ $totalCiudadanos }}</span>
                    <span class="reporteador-card-etiqueta">Ciudadanos</span>
                </div>
            </div>

            <div class="reporteador-card">
                <div class="reporteador-card-icon reporteador-card-icon--info">
                    <i class="fas fa-building-columns"></i>
                </div>
                <div class="reporteador-card-info">
                    <span class="reporteador-card-valor">{{ $totalDependencias }}</span>
                    <span class="reporteador-card-etiqueta">Dependencias</span>
                </div>
            </div>
        </div>

        {{-- Reporte de solicitudes --}}
        <div class="card">
            <div class="reporteador-table-header">
                <div>
                    <h2 class="reporteador-table-title">
                        <i class="fas fa-table-list me-2"></i>Reporte de solicitudes
                    </h2>
                    <p class="reporteador-table-sub">Todas las solicitudes registradas en la Ventanilla Única</p>
                </div>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <div class="table-responsive">
                        <table id="tabla-reporteador-solicitudes" class="table table-striped align-middle"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th class="w-documento"># solicitud</th>
                                    <th class="w-tramite"><i class="fas fa-info-circle me-2"></i>Trámite</th>
                                    <th class="w-dependencia"><i class="fas fa-building me-2"></i>Dependencia</th>
                                    <th class="w-usuario"><i class="fas fa-user me-2"></i>Ciudadano</th>
                                    <th class="w-fecha"><i class="fas fa-calendar-alt me-2"></i>Fecha de solicitud</th>
                                    <th class="w-estado"><i class="fas fa-flag me-2"></i>Estado</th>
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
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/responsive.bootstrap5.min.js"></script>
    <script>
        window.reporteadorRoutes = {
            solicitudes: "{{ route('reporteador.solicitudes') }}",
        };
    </script>
    <script src="{{ asset('js/reporteador/indexReporteador.js') }}"></script>
@endsection
