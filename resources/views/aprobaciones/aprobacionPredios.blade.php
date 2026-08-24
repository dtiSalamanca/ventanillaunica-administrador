@extends('layouts.admin')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/aprobaciones/aprobacionPredios.css') }}">
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
                    <h1 class="page-title">Aprobación de predios</h1>
                    <p class="page-subtitle">Revisa, aprueba o rechaza los predios y sus documentos cargados por los
                        usuarios</p>
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
                @php
                    $tabActivo = request('tab', 'pendientes') === 'sin-pendientes' ? 'sin-pendientes' : 'pendientes';
                @endphp

                <div class="auto-refresh-info" id="auto-refresh-info">
                    <i class="fa-solid fa-rotate"></i>
                    <span>La página se actualiza automáticamente cada 30 segundos</span>
                </div>

                <ul class="nav nav-tabs mb-3" id="tabs-aprobaciones" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $tabActivo === 'pendientes' ? 'active' : '' }}" id="pendientes-tab"
                            data-bs-toggle="tab" data-bs-target="#pendientes" type="button" role="tab"
                            aria-controls="pendientes" aria-selected="{{ $tabActivo === 'pendientes' ? 'true' : 'false' }}">
                            <i class="fa-solid fa-clock me-1"></i> Pendientes de revisión
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $tabActivo === 'sin-pendientes' ? 'active' : '' }}"
                            id="sin-pendientes-tab" data-bs-toggle="tab" data-bs-target="#sin-pendientes" type="button"
                            role="tab" aria-controls="sin-pendientes"
                            aria-selected="{{ $tabActivo === 'sin-pendientes' ? 'true' : 'false' }}">
                            <i class="fa-solid fa-check-double me-1"></i> Sin pendientes
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="tabs-aprobaciones-content">
                    <div class="tab-pane fade {{ $tabActivo === 'pendientes' ? 'show active' : '' }}" id="pendientes"
                        role="tabpanel" aria-labelledby="pendientes-tab">
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
                            @include('aprobaciones.partials.gridUsuariosPredios', [
                                'usuarios' => $pendientes,
                                'pendiente' => true,
                                'prefijo' => 'pendiente',
                                'query' => $pendientesQuery,
                            ])
                        </div>
                    </div>

                    <div class="tab-pane fade {{ $tabActivo === 'sin-pendientes' ? 'show active' : '' }}"
                        id="sin-pendientes" role="tabpanel" aria-labelledby="sin-pendientes-tab">
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
                            @include('aprobaciones.partials.gridUsuariosPredios', [
                                'usuarios' => $sinPendientes,
                                'pendiente' => false,
                                'prefijo' => 'revisado',
                                'query' => $sinPendientesQuery,
                            ])
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
        $(document).on("click", ".btn-buscar-predio", async function() {
            const cuenta = $(this).data("id");
            // Estado inicial
            Swal.fire({
                title: 'Buscando predio...',
                text: 'Validando la cuenta predial en el sistema de predial.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            try {
                const resultado = await consultarPredial(cuenta);
                if (resultado === true) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Predio aprobado',
                        html: `
                            El predio con cuenta predial 
                            <strong>${cuenta}</strong> fue encontrado
                            y <strong>aprobado</strong>.
                        `,
                        confirmButtonText: 'Aceptar'
                    });
                    location.reload();
                } else if (resultado === false) {
                    await Swal.fire({
                        icon: 'warning',
                        title: 'Predio no encontrado',
                        text: 'La cuenta predial proporcionada no existe en el sistema de predial. El predio se marcó como rechazado.',
                        confirmButtonText: 'Aceptar'
                    });
                    location.reload();
                } else {
                    await Swal.fire({
                        icon: 'error',
                        title: 'Servicio no disponible',
                        text: 'No se pudo conectar con el servicio de predial. Inténtalo nuevamente.',
                        confirmButtonText: 'Aceptar'
                    });
                }
            } catch (error) {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error inesperado',
                    text: 'Ocurrió un error al procesar la solicitud.',
                    confirmButtonText: 'Aceptar'
                });
            }
        });

        // Consulta la existencia de la cuenta predial a través del servidor
        // (evita CORS y mantiene la URL del servicio fuera del navegador).
        // Devuelve:
        //   true  -> la cuenta existe y el predio quedó marcado como validado
        //   false -> la cuenta no existe en el sistema de predial
        //   null  -> no se pudo contactar o interpretar la respuesta
        async function consultarPredial(cuenta) {
            try {
                const url = "{{ route('predio.validar', ['clave' => '__ID__']) }}".replace('__ID__', cuenta);
                const response = await fetch(url);
                const resp = await response.json();
                return resp.existe;
            } catch (error) {
                console.error(error);
                return null;
            }
        }
    </script>
    <script>
        window.aprobacionPrediosRoutes = {
            aprobarPredio: "{{ route('aprobarPredio', ['predio' => '__ID__']) }}",
            rechazarPredio: "{{ route('rechazarPredio', ['predio' => '__ID__']) }}",
            aprobarDocumento: "{{ route('aprobarDocumentoPredio', ['documentoPredio' => '__ID__']) }}",
            rechazarDocumento: "{{ route('rechazarDocumentoPredio', ['documentoPredio' => '__ID__']) }}",
            buscar: "{{ route('buscarAprobacionesPredios') }}",
        };
    </script>
    <script src="{{ asset('js/aprobaciones/aprobacionPredios.js') }}"></script>
@endsection
