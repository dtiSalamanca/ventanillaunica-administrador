@extends('layouts.admin')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/predios/indexPredios.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap5.min.css">

    <div class="main-container">
        <div class="page-header">
            <div class="header-content">
                <img src="{{ asset('images/escudoBlanco.png') }}" alt="Escudo de Salamanca" class="header-escudo">

                <div class="header-main">
                    <h1 class="page-title">Nuevas solicitudes</h1>
                    <p class="page-subtitle">Seguimiento y gestión de solicitudes</p>
                </div>

                {{-- <div class="header-actions">
                    <a href="{{ route('agregarDocumentoPredio') }}" class="btn btn-primary header-add-btn">
                        <i class="fas fa-plus me-2"></i>Agregar documento de predio
                    </a>
                </div> --}}
            </div>
        </div>

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

                <ul class="nav nav-tabs mb-3" id="tabs-solicitudes" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pendientes-tab" data-bs-toggle="tab"
                            data-bs-target="#pendientes" type="button" role="tab" aria-controls="pendientes"
                            aria-selected="true">
                            <i class="fa-solid fa-clock me-1"></i> Solicitudes pendientes
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="aprobadas-tab" data-bs-toggle="tab" data-bs-target="#aprobadas"
                            type="button" role="tab" aria-controls="aprobadas" aria-selected="false">
                            <i class="fa-solid fa-check-circle me-1"></i> Solicitudes aprobadas / turnadas
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="inactivas-tab" data-bs-toggle="tab" data-bs-target="#inactivas"
                            type="button" role="tab" aria-controls="inactivas" aria-selected="false">
                            <i class="fa-solid fa-ban me-1"></i> Solicitudes inactivas
                        </button>
                    </li>
                </ul>
                <div class="tab-content" id="tabs-solicitudes-content">
                    <div class="tab-pane fade show active" id="pendientes" role="tabpanel" aria-labelledby="pendientes-tab">
                        <div class="table-container">
                            <div class="table-responsive">
                                <table id="tabla-solicitudes-pendientes" class="table table-striped align-middle"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="w-documento"># solicitud</th>
                                            <th class="w-tramite"><i class="fas fa-info-circle me-2"></i>Trámite</th>
                                            <th class="w-usuario"><i class="fas fa-user me-2"></i>Usuario</th>
                                            <th class="w-fecha"><i class="fas fa-calendar-alt me-2"></i>Fecha de solicitud
                                            </th>
                                            <th class="w-estado"><i class="fas fa-info-circle me-2"></i>Estado</th>
                                            <th class="w-acciones"><i class="fas fa-cogs me-2"></i>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="aprobadas" role="tabpanel" aria-labelledby="aprobadas-tab">
                        <div class="table-container">
                            <div class="table-responsive">
                                <table id="tabla-solicitudes-aprobadas" class="table table-striped align-middle"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="w-documento"># solicitud</th>
                                            <th class="w-tramite"><i class="fas fa-info-circle me-2"></i>Trámite</th>
                                            <th class="w-usuario"><i class="fas fa-user me-2"></i>Usuario</th>
                                            <th class="w-fecha"><i class="fas fa-calendar-alt me-2"></i>Fecha de solicitud
                                            </th>
                                            <th class="w-estado"><i class="fas fa-info-circle me-2"></i>Estado</th>
                                            <th class="w-acciones"><i class="fas fa-cogs me-2"></i>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="inactivas" role="tabpanel" aria-labelledby="inactivas-tab">
                        <div class="table-container">
                            <div class="table-responsive">
                                <table id="tabla-solicitudes-inactivas" class="table table-striped align-middle"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="w-documento"># solicitud</th>
                                            <th class="w-tramite"><i class="fas fa-info-circle me-2"></i>Trámite</th>
                                            <th class="w-usuario"><i class="fas fa-user me-2"></i>Usuario</th>
                                            <th class="w-fecha"><i class="fas fa-calendar-alt me-2"></i>Fecha de solicitud
                                            </th>
                                            <th class="w-estado"><i class="fas fa-info-circle me-2"></i>Estado</th>
                                            <th class="w-acciones"><i class="fas fa-cogs me-2"></i>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('solicitudes.modales.modalAcciones')
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/responsive.bootstrap5.min.js"></script>
    <script>
        function initSolicitudesDataTable(tableId, filterStatus) {
            return $('#' + tableId).DataTable({
                responsive: true,
                ajax: {
                    url: "{{ route('ajax.solicitudes') }}",
                    dataSrc: function(json) {
                        return json.filter(function(item) {
                            return item.estatus_solicitud === filterStatus;
                        });
                    }
                },
                columns: [{
                        data: 'id_solicitud',
                        title: '# solicitud'
                    },
                    {
                        data: 'nombre_tramite',
                        title: 'Trámite'
                    },
                    {
                        data: 'nombre_usuario',
                        title: 'Usuario'
                    },
                    {
                        data: 'fecha_solicitud',
                        title: 'Fecha de solicitud',
                        render: function(data, type, row) {
                            if (!data) return '';
                            const dt = new Date(data);
                            const pad = (n) => String(n).padStart(2, '0');
                            return `${pad(dt.getUTCDate())}-${pad(dt.getUTCMonth() + 1)}-${dt.getUTCFullYear()} ${pad(dt.getUTCHours())}:${pad(dt.getUTCMinutes())}:${pad(dt.getUTCSeconds())}`;
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            if (row.estatus_solicitud === 0) {
                                return '<span class="badge bg-warning text-dark">Pendiente</span>';
                            } else if (row.estatus_solicitud === 1) {
                                return '<span class="badge bg-success">Aprobada / Turnada</span>';
                            } else if (row.estatus_solicitud === 2) {
                                return '<span class="badge bg-danger">Rechazada</span>';
                            } else {
                                return data;
                            }
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return '<a href="/solicitudes/' + row.id_solicitud +
                                '/detalles" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> Ver</a>';
                        },
                        orderable: false
                    }
                ]
            });
        }

        $(document).ready(function() {
            // Inicializar DataTable para solicitudes pendientes (estatus = 0)
            initSolicitudesDataTable('tabla-solicitudes-pendientes', 0);

            // Inicializar DataTable para solicitudes aprobadas/turnadas (estatus = 1)
            initSolicitudesDataTable('tabla-solicitudes-aprobadas', 1);

            // Inicializar DataTable para solicitudes inactivas/rechazadas (estatus = 2)
            initSolicitudesDataTable('tabla-solicitudes-inactivas', 2);
        });
    </script>
@endsection
