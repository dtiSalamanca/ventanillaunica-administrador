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
                    <h1 class="page-title">Documentos de Predios</h1>
                    <p class="page-subtitle">Catálogo de documentos de predios registrados en el sistema</p>
                </div>

                <div class="header-actions">
                    <a href="{{ route('agregarDocumentoPredio') }}" class="btn btn-primary header-add-btn">
                        <i class="fas fa-plus me-2"></i>Agregar documento de predio
                    </a>
                </div>
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
                            <i class="fa-solid fa-check-circle me-1"></i> Documentos activos
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="inactivos-tab" data-bs-toggle="tab" data-bs-target="#inactivos"
                            type="button" role="tab" aria-controls="inactivos" aria-selected="false">
                            <i class="fa-solid fa-ban me-1"></i> Documentos inactivos
                        </button>
                    </li>
                </ul>
                <div class="tab-content" id="tabs-documentos-predios-content">
                    <div class="tab-pane fade show active" id="activos" role="tabpanel" aria-labelledby="activos-tab">
                        <div class="action-bar">
                            <div class="action-bar-right" style="margin-left: auto;">
                                <button type="button" class="action-bar-btn btn-edit-top" id="btn-editar-documentoPredio-activos"
                                    disabled>
                                    <i class="fas fa-pen-to-square"></i> Modificar documento
                                </button>
                                <button type="button" class="action-bar-btn btn-delete-top" id="btn-deshabilitar-documentoPredio"
                                    disabled>
                                    <i class="fas fa-ban"></i> Deshabilitar
                                </button>
                            </div>
                        </div>
                        <div class="table-container">
                            <div class="table-responsive">
                                <table id="tabla-documentos-predios-activos" class="table table-striped align-middle"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="w-checkbox"></th>
                                            <th class="w-documento"><i class="fas fa-file me-2"></i>Nombre del documento</th>
                                            <th class="w-vigencia"><i class="fas fa-calendar-alt me-2"></i>Vigencia (meses)</th>
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
                                <button type="button" class="action-bar-btn btn-activate-top" id="btn-habilitar-documentoPredio"
                                    disabled>
                                    <i class="fas fa-check"></i> Habilitar
                                </button>
                            </div>
                        </div>
                        <div class="table-container">
                            <div class="table-responsive">
                                <table id="tabla-documentos-predios-inactivos" class="table table-striped align-middle"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="w-checkbox"></th>
                                            <th class="w-documento"><i class="fas fa-file me-2"></i>Nombre del documento</th>
                                            <th class="w-vigencia"><i class="fas fa-calendar-alt me-2"></i>Vigencia (meses)</th>
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
@endsection


@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/responsive.bootstrap5.min.js"></script>
    <script>
        window.documentosPrediosRoutes = {
            activos: "{{ route('getDocumentosPrediosActivos') }}",
            inactivos: "{{ route('getDocumentosPrediosInactivos') }}",
            editar: "{{ route('editarDocumentoPredio', ['documentoPredio' => '__ID__']) }}",
            deshabilitar: "{{ route('deshabilitarDocumentoPredio', ['documentoPredio' => '__ID__']) }}",
            habilitar: "{{ route('habilitarDocumentoPredio', ['documentoPredio' => '__ID__']) }}",
        };
    </script>
    <script src="{{ asset('js/predios/indexPredios.js') }}"></script>
@endsection