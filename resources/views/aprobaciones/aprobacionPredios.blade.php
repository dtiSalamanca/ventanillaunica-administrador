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
                <ul class="nav nav-tabs mb-3" id="tabs-aprobaciones" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pendientes-tab" data-bs-toggle="tab"
                            data-bs-target="#pendientes" type="button" role="tab" aria-controls="pendientes"
                            aria-selected="true">
                            <i class="fa-solid fa-clock me-1"></i> Pendientes de revisión
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sin-pendientes-tab" data-bs-toggle="tab"
                            data-bs-target="#sin-pendientes" type="button" role="tab" aria-controls="sin-pendientes"
                            aria-selected="false">
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
                            @include('aprobaciones.partials.gridUsuariosPredios', ['usuarios' => $pendientes, 'pendiente' => true, 'prefijo' => 'pendiente', 'query' => $pendientesQuery])
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
                            @include('aprobaciones.partials.gridUsuariosPredios', ['usuarios' => $sinPendientes, 'pendiente' => false, 'prefijo' => 'revisado', 'query' => $sinPendientesQuery])
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
        $(document).on("click", ".btn-buscar-predio", async function () {
            const cuenta = $(this).data("id");
            // Estado inicial
            Swal.fire({
                title: 'Buscando predio...',
                text: 'Validando la cuenta catastral en el sistema de predial.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            try {
                const resultado = await existeCuenta(cuenta);
                if (resultado === true) {
                    // Cambiamos el estado del mismo Swal
                    Swal.update({
                        icon: 'info',
                        title: 'Predio encontrado',
                        text: 'Actualizando información del predio...'
                    });
                    const url = "{{ route('predio.validar', ['id' => '__ID__']) }}".replace('__ID__', cuenta);
                    const actPredio = await fetch(url);
                    if (actPredio.ok) {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Proceso completado',
                            html: `
                                El predio con cuenta catastral 
                                <strong>${cuenta}</strong> fue encontrado 
                                y actualizado correctamente.
                            `,
                            confirmButtonText: 'Aceptar'
                        });
                        location.reload();
                    } else {
                        await Swal.fire({
                            icon: 'error',
                            title: 'Error al actualizar',
                            text: 'El predio existe, pero ocurrió un problema al actualizar la información.',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                } else {
                    await Swal.fire({
                        icon: 'error',
                        title: 'Predio no encontrado',
                        text: 'La cuenta catastral proporcionada no existe en el sistema de predial.',
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

        async function existeCuenta(cuenta) {
            try {
                const response = await fetch(`http://localhost:8001/api/puurbanos/${cuenta}/existe`);
                const resp = await response.json();
                if (resp.existe === true) {
                    return true;
                } else {
                    return false;
                }
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