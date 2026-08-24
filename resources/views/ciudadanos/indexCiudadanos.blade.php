@extends('layouts.admin')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/ciudadanos/indexCiudadanos.css') }}">
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
                    <h1 class="page-title">Ciudadanos</h1>
                    <p class="page-subtitle">Consulta de ciudadanos registrados en la Ventanilla Única</p>
                </div>

                <div class="header-actions">
                    <button type="button" class="btn btn-primary header-add-btn" id="btn-recargar-ciudadanos">
                        <i class="fas fa-rotate-right me-2"></i>Recargar ciudadanos
                    </button>
                </div>
            </div>
        </div>

        <div id="alertas-dinamicas"></div>

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
                <ul class="nav nav-tabs mb-3" id="tabs-ciudadanos" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="activos-tab" data-bs-toggle="tab" data-bs-target="#activos"
                            type="button" role="tab" aria-controls="activos" aria-selected="true">
                            <i class="fa-solid fa-circle-check me-1"></i> Ciudadanos activos
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sin-verificar-tab" data-bs-toggle="tab" data-bs-target="#sin-verificar"
                            type="button" role="tab" aria-controls="sin-verificar" aria-selected="false">
                            <i class="fa-solid fa-envelope me-1"></i> Sin verificar correo
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="bloqueados-tab" data-bs-toggle="tab" data-bs-target="#bloqueados"
                            type="button" role="tab" aria-controls="bloqueados" aria-selected="false">
                            <i class="fa-solid fa-ban me-1"></i> Ciudadanos bloqueados
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="tabs-ciudadanos-content">
                    <div class="tab-pane fade show active" id="activos" role="tabpanel" aria-labelledby="activos-tab">
                        <div class="action-bar">
                            <div class="action-bar-left">
                                <span id="selection-count-activos">0 ciudadano(s) seleccionado(s)</span>
                            </div>
                            <div class="action-bar-right">
                                <button type="button" class="action-bar-btn btn-bloquear-ciudadanos"
                                    id="btn-bloquear-ciudadanos" disabled>
                                    <i class="fas fa-ban me-1"></i> Bloquear
                                </button>
                            </div>
                        </div>
                        <div class="table-container">
                            <div class="table-responsive">
                                <table id="tabla-ciudadanos-activos" class="table table-striped align-middle"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="w-checkbox">SEL.</th>
                                            <th class="w-nombre"><i class="fas fa-id-card me-2"></i>Nombre completo</th>
                                            <th class="w-correo"><i class="fas fa-envelope me-2"></i>Correo electrónico
                                            </th>
                                            <th class="w-registro"><i class="fas fa-calendar me-2"></i>Fecha de registro
                                            </th>
                                            <th class="w-estado"><i class="fas fa-flag me-2"></i>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Datos cargados por AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="sin-verificar" role="tabpanel" aria-labelledby="sin-verificar-tab">
                        <div class="action-bar">
                            <div class="action-bar-left">
                                <span id="selection-count-sin-verificar">0 ciudadano(s) seleccionado(s)</span>
                            </div>
                            <div class="action-bar-right">
                                <button type="button" class="action-bar-btn btn-bloquear-ciudadanos"
                                    id="btn-bloquear-sin-verificar" disabled>
                                    <i class="fas fa-ban me-1"></i> Bloquear
                                </button>
                            </div>
                        </div>
                        <div class="table-container">
                            <div class="table-responsive">
                                <table id="tabla-ciudadanos-sin-verificar" class="table table-striped align-middle"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="w-checkbox">SEL.</th>
                                            <th class="w-nombre"><i class="fas fa-id-card me-2"></i>Nombre completo</th>
                                            <th class="w-correo"><i class="fas fa-envelope me-2"></i>Correo electrónico
                                            </th>
                                            <th class="w-registro"><i class="fas fa-calendar me-2"></i>Fecha de registro
                                            </th>
                                            <th class="w-estado"><i class="fas fa-flag me-2"></i>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Datos cargados por AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="bloqueados" role="tabpanel" aria-labelledby="bloqueados-tab">
                        <div class="action-bar">
                            <div class="action-bar-left">
                                <span id="selection-count-bloqueados">0 ciudadano(s) seleccionado(s)</span>
                            </div>
                            <div class="action-bar-right">
                                <button type="button" class="action-bar-btn btn-desbloquear-ciudadanos"
                                    id="btn-desbloquear-ciudadanos" disabled>
                                    <i class="fas fa-rotate-left me-1"></i> Desbloquear
                                </button>
                            </div>
                        </div>
                        <div class="table-container">
                            <div class="table-responsive">
                                <table id="tabla-ciudadanos-bloqueados" class="table table-striped align-middle"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="w-checkbox">SEL.</th>
                                            <th class="w-nombre"><i class="fas fa-id-card me-2"></i>Nombre completo</th>
                                            <th class="w-correo"><i class="fas fa-envelope me-2"></i>Correo electrónico
                                            </th>
                                            <th class="w-registro"><i class="fas fa-calendar me-2"></i>Fecha de registro
                                            </th>
                                            <th class="w-estado"><i class="fas fa-flag me-2"></i>Estado</th>
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
        window.ciudadanosRoutes = {
            index: "{{ route('getCiudadanos') }}",
            bloquear: "{{ route('bloquearCiudadanos') }}",
            desbloquear: "{{ route('desbloquearCiudadanos') }}",
        };
    </script>
    <script src="{{ asset('js/ciudadanos/indexCiudadanos.js') }}"></script>
@endsection
