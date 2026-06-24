@extends('layouts.admin')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/requisitos/revisarRequisitos.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">

    <div class="main-container">
        <!-- Header -->
        <div class="page-header">
            <div class="header-content">
                <img src="{{ asset('images/escudoBlanco.png') }}" alt="Escudo de Salamanca" class="header-escudo">

                <div class="header-main">
                    <h1 class="page-title">Requisitos del trámite</h1>
                    <p class="page-subtitle">{{ $tramite->nombre }}</p>
                </div>

                <div class="header-actions">
                    <a href="{{ route('indexTramites') }}" class="btn btn-primary header-back-btn me-2">
                        <i class="fas fa-arrow-left me-2"></i>Regresar
                    </a>
                    <button type="button" class="btn btn-primary header-add-btn"
                        data-bs-toggle="modal" data-bs-target="#modalAsignarRequisitos">
                        <i class="fas fa-plus me-2"></i>Asignar requisitos
                    </button>
                </div>
            </div>
        </div>

        <!-- Alertas de sesión -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Card principal -->
        <div class="card">
            <div class="card-body">

                <!-- Barra de acciones -->
                <div class="action-bar">
                    <div class="action-bar-right" style="margin-left: auto;">
                        <button type="button" class="action-bar-btn btn-delete-top"
                            id="btn-quitar-requisito" disabled>
                            <i class="fas fa-xmark"></i> Quitar del trámite
                        </button>
                    </div>
                </div>

                <!-- Tabla de requisitos asignados -->
                <div class="table-container">
                    <div class="table-responsive">
                        <table id="tabla-requisitos-asignados"
                            class="table table-striped align-middle" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="w-checkbox"></th>
                                    <th><i class="fas fa-file-lines me-2"></i>Nombre del requisito</th>
                                    <th class="w-estado">Estado en catálogo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Datos cargados por AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal asignar requisitos -->
    <div class="modal fade" id="modalAsignarRequisitos" tabindex="-1"
        aria-labelledby="modalAsignarRequisitosLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAsignarRequisitosLabel">
                        <i class="fas fa-file-lines me-2"></i>Asignar requisitos al trámite
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modal-alert" class="alert alert-danger d-none mb-3"></div>
                    <div class="mb-3">
                        <label for="select-requisitos" class="form-label fw-semibold">
                            Seleccionar del catálogo <span class="text-danger">*</span>
                        </label>
                        <select id="select-requisitos" class="form-control" multiple="multiple"
                            style="width:100%">
                        </select>
                        <div class="form-text mt-2">
                            <i class="fas fa-circle-info me-1 text-muted"></i>
                            Solo se muestran requisitos activos del catálogo que aún no están asignados a este trámite.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-guardar" id="btn-guardar-asignacion">
                        <i class="fas fa-save me-1"></i> Asignar
                    </button>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/es.js"></script>
    <script>
        window.requisitosRoutes = {
            asignados: "{{ route('getRequisitosAsignados', ['tramite' => $tramite->id_tramite]) }}",
            catalogo: "{{ route('getCatalogoDisponible', ['tramite' => $tramite->id_tramite]) }}",
            asignar: "{{ route('asignarRequisitos', ['tramite' => $tramite->id_tramite]) }}",
            quitar: "{{ route('quitarRequisito', ['tramite' => $tramite->id_tramite, 'requisito' => '__ID__']) }}",
        };
    </script>
    <script src="{{ asset('js/requisitos/revisarRequisitos.js') }}"></script>
@endsection
