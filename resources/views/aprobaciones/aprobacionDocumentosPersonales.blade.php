@extends('layouts.admin')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/aprobaciones/aprobacionDocumentosPersonales.css') }}">
    <!-- Fuentes y librerías -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="main-container">
        <!-- Header -->
        <div class="page-header">
            <div class="header-content">
                <img src="{{ asset('images/escudoBlanco.png') }}" alt="Escudo de Salamanca" class="header-escudo">

                <div class="header-main">
                    <h1 class="page-title">Aprobación de documentos personales</h1>
                    <p class="page-subtitle">Revisa, aprueba o rechaza los documentos personales cargados por los usuarios</p>
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
                <!-- Tabs -->
                <ul class="nav nav-tabs mb-3" id="tabs-aprobaciones" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pendientes-tab" data-bs-toggle="tab" data-bs-target="#pendientes"
                            type="button" role="tab" aria-controls="pendientes" aria-selected="true">
                            <i class="fa-solid fa-clock me-1"></i> Pendientes de revisión
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sin-pendientes-tab" data-bs-toggle="tab" data-bs-target="#sin-pendientes"
                            type="button" role="tab" aria-controls="sin-pendientes" aria-selected="false">
                            <i class="fa-solid fa-check-double me-1"></i> Sin pendientes
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="tabs-aprobaciones-content">
                    <div class="tab-pane fade show active" id="pendientes" role="tabpanel" aria-labelledby="pendientes-tab">
                        <div class="aprobaciones-search">
                            <div class="search-bar">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" id="pendientes-search-input" value="{{ $pendientesQuery }}"
                                    placeholder="Buscar por nombre o correo electrónico..." autocomplete="off">
                                <button type="button" id="pendientes-search-clear" class="search-clear"
                                    title="Limpiar búsqueda" style="{{ $pendientesQuery ? '' : 'display:none' }}">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </div>

                        <div id="pendientes-resultado">
                            @include('aprobaciones.partials.gridUsuarios', ['usuarios' => $pendientes, 'pendiente' => true, 'prefijo' => 'pendiente', 'query' => $pendientesQuery])
                        </div>
                    </div>

                    <div class="tab-pane fade" id="sin-pendientes" role="tabpanel" aria-labelledby="sin-pendientes-tab">
                        <div class="aprobaciones-search">
                            <div class="search-bar">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" id="sin-pendientes-search-input" value="{{ $sinPendientesQuery }}"
                                    placeholder="Buscar por nombre o correo electrónico..." autocomplete="off">
                                <button type="button" id="sin-pendientes-search-clear" class="search-clear"
                                    title="Limpiar búsqueda" style="{{ $sinPendientesQuery ? '' : 'display:none' }}">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </div>

                        <div id="sin-pendientes-resultado">
                            @include('aprobaciones.partials.gridUsuarios', ['usuarios' => $sinPendientes, 'pendiente' => false, 'prefijo' => 'revisado', 'query' => $sinPendientesQuery])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.aprobacionDocumentosPersonalesRoutes = {
            aprobar: "{{ route('aprobarDocumentoPersonal', ['documentoPersonal' => '__ID__']) }}",
            rechazar: "{{ route('rechazarDocumentoPersonal', ['documentoPersonal' => '__ID__']) }}",
            buscar: "{{ route('buscarAprobacionesDocumentosPersonales') }}",
        };
    </script>
    <script src="{{ asset('js/aprobaciones/aprobacionDocumentosPersonales.js') }}"></script>
@endsection
