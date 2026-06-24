@extends('layouts.admin')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/requisitos/indexRequisitos.css') }}">
    <!-- Fuentes y librerías -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap5.min.css">

    <div class="main-container">
        <!-- Header -->
        <div class="page-header">
            <div class="header-content">
                <img src="{{ asset('images/escudoBlanco.png') }}" alt="Escudo de Salamanca" class="header-escudo">

                <div class="header-main">
                    <h1 class="page-title">Requisitos</h1>
                    <p class="page-subtitle">Consulta de requisitos registrados en el sistema</p>
                </div>

                <div class="header-actions">
                    <a href="{{ route('agregarRequisito') }}" class="btn btn-primary header-add-btn">
                        <i class="fas fa-plus me-2"></i>Agregar requisito
                    </a>
                </div>
            </div>
        </div>

        <!-- Alertas -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-times-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Card principal -->
        <div class="card">

            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger mb-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Tabs -->
                <ul class="nav nav-tabs mb-3" id="tabs-requisitos" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="activos-tab" data-bs-toggle="tab" data-bs-target="#activos"
                            type="button" role="tab" aria-controls="activos" aria-selected="true">
                            <i class="fa-solid fa-check-circle me-1"></i> Requisitos activos
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="inactivos-tab" data-bs-toggle="tab" data-bs-target="#inactivos"
                            type="button" role="tab" aria-controls="inactivos" aria-selected="false">
                            <i class="fa-solid fa-ban me-1"></i> Requisitos inactivos
                        </button>
                    </li>
                </ul>
                <div class="tab-content" id="tabs-requisitos-content">
                    <div class="tab-pane fade show active" id="activos" role="tabpanel" aria-labelledby="activos-tab">
                        <!-- Barra de acciones -->
                        <div class="action-bar">
                            <div class="action-bar-right" style="margin-left: auto;">
                                <button type="button" class="action-bar-btn btn-edit-top" id="btn-editar-requisito-activos"
                                    disabled>
                                    <i class="fas fa-pen-to-square"></i> Modificar requisito
                                </button>
                                <button type="button" class="action-bar-btn btn-delete-top" id="btn-deshabilitar-requisito"
                                    disabled>
                                    <i class="fas fa-ban"></i> Deshabilitar
                                </button>
                            </div>
                        </div>
                        <div class="table-container">
                            <div class="table-responsive">
                                <table id="tabla-requisitos-activos" class="table table-striped align-middle"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="w-checkbox"></th>
                                            <th class="w-requisito"><i class="fas fa-building me-2"></i>Nombre del requisito</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Datos cargados por AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="inactivos" role="tabpanel" aria-labelledby="inactivos-tab">
                        <!-- Barra de acciones -->
                        <div class="action-bar">
                            <div class="action-bar-right" style="margin-left: auto;">
                                <button type="button" class="action-bar-btn btn-activate-top" id="btn-habilitar-requisito"
                                    disabled>
                                    <i class="fas fa-check"></i> Habilitar
                                </button>
                            </div>
                        </div>
                        <div class="table-container">
                            <div class="table-responsive">
                                <table id="tabla-requisitos-inactivos" class="table table-striped align-middle"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="w-checkbox"></th>
                                            <th class="w-requisito"><i class="fas fa-building me-2"></i>Nombre del requisito</th>
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
        window.requisitosRoutes = {
            activas: "{{ route('getRequisitosActivos') }}",
            inactivas: "{{ route('getRequisitosInactivos') }}",
            {{-- editar, deshabilitar, habilitar pendientes --}}
            deshabilitar: "{{ route('deshabilitarRequisito', ['requisito' => '__ID__']) }}",
            habilitar: "{{ route('habilitarRequisito', ['requisito' => '__ID__']) }}",
        };
    </script>
    <script src="{{ asset('js/requisitos/indexRequisitos.js') }}"></script>
@endsection
