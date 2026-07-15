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
                    <p class="page-subtitle">Seguimiento para las solicitudes activas</p>
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

                <ul class="nav nav-tabs mb-3" id="tabs-documentos-predios" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="activos-tab" data-bs-toggle="tab" data-bs-target="#activos"
                            type="button" role="tab" aria-controls="activos" aria-selected="true">
                            <i class="fa-solid fa-check-circle me-1"></i> Solicitudes activas
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="inactivos-tab" data-bs-toggle="tab" data-bs-target="#inactivos"
                            type="button" role="tab" aria-controls="inactivos" aria-selected="false">
                            <i class="fa-solid fa-ban me-1"></i> Solicitudes inactivas
                        </button>
                    </li>
                </ul>
                <div class="tab-content" id="tabs-documentos-predios-content">
                    <div class="tab-pane fade show active" id="activos" role="tabpanel" aria-labelledby="activos-tab">
                        <div class="action-bar">
                            <div class="action-bar-right" style="margin-left: auto;">
                                <button type="button" class="action-bar-btn btn-edit-top"
                                    id="btn-editar-documentoPredio-activos" disabled>
                                    <i class="fas fa-pen-to-square"></i> Modificar documento
                                </button>
                                <button type="button" class="action-bar-btn btn-delete-top"
                                    id="btn-deshabilitar-documentoPredio" disabled>
                                    <i class="fas fa-ban"></i> Deshabilitar
                                </button>
                            </div>
                        </div>
                        <div class="table-container">
                            <div class="table-responsive">
                                <table id="tabla-solicitudes-activas" class="table table-striped align-middle"
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
                    <div class="tab-pane fade" id="inactivos" role="tabpanel" aria-labelledby="inactivos-tab">
                        <div class="action-bar">
                            <div class="action-bar-right" style="margin-left: auto;">
                                <button type="button" class="action-bar-btn btn-activate-top"
                                    id="btn-habilitar-documentoPredio" disabled>
                                    <i class="fas fa-check"></i> Habilitar
                                </button>
                            </div>
                        </div>
                        <div class="table-container">
                            <div class="table-responsive">
                                <table id="tabla-solicitudes-inactivas" class="table table-striped align-middle"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="w-checkbox"></th>
                                            <th class="w-documento"><i class="fas fa-file me-2"></i>Nombre del documento
                                            </th>
                                            <th class="w-vigencia"><i class="fas fa-calendar-alt me-2"></i>Vigencia (meses)
                                            </th>
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
        $(document).ready(function () {
            // Inicializar DataTables para solicitudes activas
            $('#tabla-solicitudes-activas').DataTable({
                responsive: true,
                ajax: {
                    url: "{{ route('ajax.solicitudes') }}",
                    dataSrc: ''
                },
                columns: [
                    { data: 'id_solicitud', title: '# solicitud' },
                    { data: 'nombre_tramite', title: 'Trámite' },
                    { data: 'nombre_usuario', title: 'Usuario' },
                    {
                        data: 'fecha_solicitud',
                        title: 'Fecha de solicitud',
                        render: function (data, type, row) {
                            if (!data) return '';
                            const dt = new Date(data);
                            const pad = (n) => String(n).padStart(2, '0');
                            return `${pad(dt.getUTCDate())}-${pad(dt.getUTCMonth() + 1)}-${dt.getUTCFullYear()} ${pad(dt.getUTCHours())}:${pad(dt.getUTCMinutes())}:${pad(dt.getUTCSeconds())}`;
                        }
                    },
                    {
                        //data: 'estatus_solicitud', title: 'Estado' 
                        data: null, render: function (data, type, row) {
                            if (row.estatus_solicitud === 0) {
                                return '<span class="badge bg-warning text-dark">Pendiente</span>';
                            } else if (row.estatus_solicitud === 1) {
                                return '<span class="badge bg-success">Aprobada</span>';
                            } else if (row.estatus_solicitud === 2) {
                                return '<span class="badge bg-danger">Rechazada</span>';
                            } else {
                                return data;
                            }
                        }
                    },
                    {
                        data: null, render: function (data, type, row) {
                            return '<button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAcciones" onclick="loadDocumentos(' + row.id_solicitud + ')">Ver</button>';
                        }, orderable: false
                    }
                ]
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
                tbody.innerHTML = '<tr><td colspan="3" class="text-center">No hay documentos para esta solicitud.</td></tr>';
                return;
            }

            documentos.forEach((doc, index) => {
                const estatusBadge = doc.entregado
                    ? '<span class="badge bg-success">Entregado</span>'
                    : '<span class="badge bg-warning text-dark">Pendiente</span>';

                const accion = doc.entregado
                    ? `<a href="${doc.ruta_documento}" target="_blank" rel="noopener" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> Ver
                        </a>`
                    : '<span class="text-muted">Sin documento</span>';

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