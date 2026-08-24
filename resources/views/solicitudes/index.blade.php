@extends('layouts.admin')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/predios/indexPredios.css') }}">
    <style>
        .filter-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .pill-estatus-turnadas {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 999px;
            padding: 0.35rem 0.9rem;
            font-size: 0.82rem;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s;
        }

        .pill-estatus-turnadas:hover {
            background: #e5e7eb;
            border-color: #9ca3af;
        }

        .pill-estatus-turnadas.active {
            background: #6b2a2a;
            border-color: #6b2a2a;
            color: #fff;
        }

        .pill-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
        }

        .pill-count {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 999px;
            padding: 0.05rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .pill-estatus-turnadas.active .pill-count {
            background: rgba(255, 255, 255, 0.25);
        }
    </style>
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
                            <i class="fa-solid fa-check-circle me-1"></i> Solicitudes turnadas
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="inactivas-tab" data-bs-toggle="tab" data-bs-target="#inactivas"
                            type="button" role="tab" aria-controls="inactivas" aria-selected="false">
                            <i class="fa-solid fa-ban me-1"></i> Rechazadas
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
                        <!-- Filtros por estatus -->
                        <div class="filter-pills mb-3" id="filtros-estatus-turnadas">
                            <button type="button" class="pill-estatus-turnadas active" data-estatus="todos">
                                <span class="pill-dot" style="background:#6c757d"></span>Todos
                                <span class="pill-count" id="count-todos">0</span>
                            </button>
                            <button type="button" class="pill-estatus-turnadas" data-estatus="1">
                                <span class="pill-dot" style="background:#10b981"></span>Turnados
                                <span class="pill-count" id="count-1">0</span>
                            </button>
                            <button type="button" class="pill-estatus-turnadas" data-estatus="2">
                                <span class="pill-dot" style="background:#ef4444"></span>Rechazados
                                <span class="pill-count" id="count-2">0</span>
                            </button>
                            <button type="button" class="pill-estatus-turnadas" data-estatus="3">
                                <span class="pill-dot" style="background:#0ea5e9"></span>Por pagar
                                <span class="pill-count" id="count-3">0</span>
                            </button>
                            <button type="button" class="pill-estatus-turnadas" data-estatus="4">
                                <span class="pill-dot" style="background:#3b82f6"></span>Completados
                                <span class="pill-count" id="count-4">0</span>
                            </button>
                        </div>
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
        var solicitudesEndpoint = "{{ route('ajax.solicitudes.completas') }}";

        function renderEstatus(row) {
            var estatus = row.estatus_mostrado !== undefined ? row.estatus_mostrado : row.estatus_solicitud;
            if (estatus === 0) {
                return '<span class="badge bg-warning text-dark">Pendiente</span>';
            } else if (estatus === 1) {
                return '<span class="badge bg-success">Turnada</span>';
            } else if (estatus === 2) {
                return '<span class="badge bg-danger">Rechazada</span>';
            } else if (estatus === 3) {
                return '<span class="badge bg-info text-dark">Por pagar</span>';
            } else if (estatus === 4) {
                return '<span class="badge bg-primary">Completado</span>';
            }
            return '<span class="badge bg-secondary">Desconocido</span>';
        }

        function renderAcciones(row) {
            var url = '/solicitudes/' + row.id_solicitud + '/detalles';
            return '<a href="' + url + '" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> Ver</a>';
        }

        var filtroEstatusTurnadas = 'todos';
        var conteosTurnadasCalculados = false;

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'tabla-solicitudes-aprobadas') {
                return true;
            }

            if (filtroEstatusTurnadas === 'todos') {
                return true;
            }

            var row = settings.aoData[dataIndex]._aData;
            var estatus = row.estatus_mostrado !== undefined ? row.estatus_mostrado : row.estatus_solicitud;
            return String(estatus) === String(filtroEstatusTurnadas);
        });

        function initSolicitudesDataTable(tableId, filterFn, onData) {
            return $('#' + tableId).DataTable({
                responsive: true,
                language: {
                    processing: "Procesando...",
                    lengthMenu: "Mostrar _MENU_ registros",
                    zeroRecords: "No se encontraron resultados",
                    emptyTable: "Ninguna solicitud disponible en esta tabla",
                    info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
                    infoFiltered: "(filtrado de un total de _MAX_ registros)",
                    search: "Buscar:",
                    infoThousands: ",",
                    loadingRecords: "Cargando...",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    }
                },
                ajax: {
                    url: solicitudesEndpoint,
                    dataSrc: function(json) {
                        var rows = json.filter(filterFn);
                        if (onData) {
                            onData(rows);
                        }
                        return rows;
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
                            // El valor viene de la BD ya en hora de México (America/Mexico_City),
                            // así que se formatea directamente sin conversión de zona horaria.
                            const [fecha, hora] = data.split(' ');
                            if (!hora) return data;
                            const [anio, mes, dia] = fecha.split('-');

                            // Conversión al formato de 12 horas (a.m./p.m.)
                            const [h, m] = hora.split(':').map(Number);
                            const horas12 = h % 12 === 0 ? 12 : h % 12;
                            const periodo = h < 12 ? 'a.m.' : 'p.m.';
                            const pad = (n) => String(n).padStart(2, '0');

                            return `${dia}-${mes}-${anio} ${pad(horas12)}:${pad(m)} ${periodo}`;
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return renderEstatus(row);
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return renderAcciones(row);
                        },
                        orderable: false
                    }
                ]
            });
        }

        $(document).ready(function() {
            // Pendientes (estatus = 0): necesita revisión del admin
            initSolicitudesDataTable('tabla-solicitudes-pendientes', function(item) {
                return item.estatus_solicitud === 0;
            });

            // Turnadas (estatus >= 1 con turnado): todas las que el admin turnó
            initSolicitudesDataTable('tabla-solicitudes-aprobadas', function(item) {
                return item.has_turnado == 1;
            }, function(rows) {
                if (conteosTurnadasCalculados) {
                    return;
                }

                var conteos = {
                    todos: rows.length,
                    1: 0,
                    2: 0,
                    3: 0,
                    4: 0
                };

                rows.forEach(function(item) {
                    var estatus = item.estatus_mostrado !== undefined ? item.estatus_mostrado : item
                        .estatus_solicitud;
                    if (Object.prototype.hasOwnProperty.call(conteos, estatus)) {
                        conteos[estatus]++;
                    }
                });

                Object.keys(conteos).forEach(function(estatus) {
                    var contador = document.getElementById('count-' + estatus);
                    if (contador) {
                        contador.textContent = conteos[estatus];
                    }
                });

                conteosTurnadasCalculados = true;
            });

            // Rechazadas: solo las que el admin rechazó directamente (sin turnado)
            initSolicitudesDataTable('tabla-solicitudes-inactivas', function(item) {
                return item.estatus_solicitud === 2 && item.has_turnado == 0;
            });

            // Filtros de estatus en el tab de turnadas
            $(document).on('click', '.pill-estatus-turnadas', function() {
                filtroEstatusTurnadas = $(this).data('estatus');
                $('.pill-estatus-turnadas').removeClass('active');
                $(this).addClass('active');
                $('#tabla-solicitudes-aprobadas').DataTable().draw();
            });
        });

        async function loadDocumentos(id_solicitud) {
            try {
                const response = await fetch(`/ajax/solicitud/${id_solicitud}`);

                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }

                const data = await response.json();
                renderDocumentosEnModal(data);

            } catch (error) {
                console.error("Error al cargar los documentos:", error);
            }
        }

        function renderDocumentosEnModal(documentos) {
            const tbody = document.querySelector('#documentosBody');
            tbody.innerHTML = ''; // limpiar contenido anterior

            if (documentos.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="3" class="text-center">No hay documentos para esta solicitud.</td></tr>';
                return;
            }

            documentos.forEach((doc, index) => {
                const estatusBadge = doc.entregado ?
                    '<span class="badge bg-success">Entregado</span>' :
                    '<span class="badge bg-warning text-dark">Pendiente</span>';

                const accion = doc.entregado ?
                    `<a href="${doc.ruta_documento}" target="_blank" rel="noopener" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> Ver
                        </a>` :
                    '<span class="text-muted">Sin documento</span>';

                tbody.innerHTML += `
                    <tr>
                        <th scope="row">${index + 1}</th>
                        <td>${doc.nombre_requisito} ${estatusBadge}</td>
                        <td>${accion}</td>
                    </tr>
                `;
            });
        }
    </script>
@endsection
