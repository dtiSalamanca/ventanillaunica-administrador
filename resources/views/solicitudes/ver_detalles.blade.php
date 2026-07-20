@extends('layouts.admin')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/solicitudes/verDetalles.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="detalles-container">
        {{-- Header --}}
        <div class="detalles-header">
            <div class="detalles-header-icon">
                <i class="fas fa-file-lines"></i>
            </div>
            <div class="detalles-header-text">
                <h1>Detalles de la solicitud #{{ $solicitud->id_solicitud }}</h1>
                <p>Información completa de la solicitud y documentos presentados</p>
            </div>
        </div>

        {{-- Card: Información del solicitante --}}
        <div class="detalles-card">
            <div class="detalles-card-header">
                <i class="fas fa-user-circle"></i> Datos del solicitante
            </div>
            <div class="detalles-card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-user me-1"></i> Nombre</span>
                        <span class="info-value">{{ $solicitud->user?->name ?? 'Sin nombre' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-envelope me-1"></i> Correo electrónico</span>
                        <span class="info-value">{{ $solicitud->user?->email ?? 'Sin correo' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-calendar-alt me-1"></i> Fecha de solicitud</span>
                        <span
                            class="info-value">{{ $solicitud->fecha_solicitud ? \Carbon\Carbon::parse($solicitud->fecha_solicitud)->format('d-m-Y H:i:s') : 'Sin fecha' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-briefcase me-1"></i> Trámite solicitado</span>
                        <span class="info-value">{{ $solicitud->tramite?->nombre_tramite ?? 'Sin trámite' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-flag me-1"></i> Estado</span>
                        <span class="info-value">
                            @if ($solicitud->estatus_solicitud === 0)
                                <span class="estado-badge estado-pendiente"><i
                                        class="fas fa-clock me-1"></i>Pendiente</span>
                            @elseif ($solicitud->estatus_solicitud === 1)
                                <span class="estado-badge estado-aprobada"><i
                                        class="fas fa-check-circle me-1"></i>Aprobada</span>
                            @elseif ($solicitud->estatus_solicitud === 2)
                                <span class="estado-badge estado-rechazada"><i
                                        class="fas fa-times-circle me-1"></i>Rechazada</span>
                            @else
                                <span class="estado-badge">Desconocido</span>
                            @endif
                        </span>
                    </div>
                    @if ($solicitud->observacion_solicitud)
                        <div class="info-item full-width">
                            <span class="info-label"><i class="fas fa-comment me-1"></i> Observación</span>
                            <span class="info-value">{{ $solicitud->observacion_solicitud }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card: Requisitos y documentos presentados --}}
        <div class="detalles-card">
            <div class="detalles-card-header">
                <i class="fas fa-file-alt"></i> Requisitos y documentos presentados
            </div>
            <div class="detalles-card-body">
                @if ($requisitosTramite->count() > 0)
                    <div class="requisitos-table-wrapper">
                        <table class="requisitos-table">
                            <thead>
                                <tr>
                                    <th class="req-num">#</th>
                                    <th class="req-nombre">Requisito</th>
                                    <th class="req-archivo">Documento</th>
                                    <th class="req-accion">Ver</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requisitosTramite as $index => $docTramite)
                                    @php
                                        $archivo = null;
                                        $tipoDocumento = null;
                                        if ($docTramite->documentoSolicitud) {
                                            $archivo = $docTramite->documentoSolicitud->documento_solicitud;
                                            $tipoDocumento = 'solicitud';
                                        } elseif ($docTramite->documentoPersonal) {
                                            $archivo = $docTramite->documentoPersonal->ruta_archivo;
                                            $tipoDocumento = 'personal';
                                        } else {
                                            // Buscar en documentos de predio por nombre del requisito
                                            $reqNombre = $docTramite->requisito?->nombre_requisito ?? '';
                                            $predioDoc = $predioDocs->first(function ($pd) use ($reqNombre) {
                                                return $pd->catalogoDocumento &&
                                                    strcasecmp(
                                                        trim($pd->catalogoDocumento->nombre_documento),
                                                        trim($reqNombre),
                                                    ) === 0;
                                            });
                                            if ($predioDoc) {
                                                $archivo = $predioDoc->ruta_documento;
                                                $tipoDocumento = 'predio';
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td class="req-num">{{ $index + 1 }}</td>
                                        <td class="req-nombre">
                                            <div class="req-nombre-texto">
                                                {{ $docTramite->requisito?->nombre_requisito ?? 'Requisito #' . $docTramite->fk_requisito }}
                                            </div>
                                        </td>
                                        <td class="req-archivo">
                                            @if ($archivo)
                                                <span class="doc-subido">
                                                    <i class="fas fa-file-pdf"></i>
                                                    {{ basename($archivo) }}
                                                </span>
                                            @else
                                                <span class="doc-no-subido">
                                                    <i class="fas fa-times-circle me-1"></i> No adjuntado
                                                </span>
                                            @endif
                                        </td>
                                        <td class="req-accion">
                                            @if ($archivo)
                                                <button type="button" class="btn-ver-archivo" title="Ver documento"
                                                    onclick="verArchivo('{{ $archivo }}')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-file-circle-exclamation"></i>
                        <p>No hay requisitos registrados para esta solicitud.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Card: Acciones (solo si está pendiente) --}}
        @if ($solicitud->estatus_solicitud === 0)
            <div class="detalles-card">
                <div class="detalles-card-header">
                    <i class="fas fa-gavel"></i> Resolver solicitud
                </div>
                <div class="detalles-card-body">
                    {{-- Botones principales --}}
                    <div class="acciones-container">
                        <button type="button" class="btn-accion btn-aprobar" id="btnAprobar">
                            <i class="fas fa-check-circle"></i> Aprobar y turnar
                        </button>
                        <button type="button" class="btn-accion btn-rechazar" id="btnRechazar">
                            <i class="fas fa-times-circle"></i> Rechazar
                        </button>
                        <a href="{{ route('solicitudes.index') }}" class="btn-accion btn-regresar">
                            <i class="fas fa-arrow-left"></i> Regresar
                        </a>
                    </div>

                    {{-- Panel de aprobación con turnado --}}
                    <div class="turnado-panel" id="turnadoPanel">
                        <div class="turnado-header">
                            <i class="fas fa-exchange-alt me-1"></i> Turnar solicitud
                        </div>
                        <div class="turnado-body">
                            <div class="turnado-grid">
                                <div class="turnado-field">
                                    <label for="selectDependencia">
                                        <i class="fas fa-building me-1"></i> Dependencia:
                                    </label>
                                    <select id="selectDependencia" class="turnado-select">
                                        <option value="">— Seleccionar dependencia —</option>
                                        @foreach ($dependencias as $dep)
                                            <option value="{{ $dep->id_dependencia }}">{{ $dep->nombre_dependencia }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="turnado-field">
                                    <label for="selectUsuario">
                                        <i class="fas fa-user-tie me-1"></i> Usuario responsable:
                                    </label>
                                    <select id="selectUsuario" class="turnado-select" disabled>
                                        <option value="">— Primero selecciona una dependencia —</option>
                                    </select>
                                </div>
                            </div>
                            <div class="turnado-actions">
                                <button type="button" class="btn-confirmar-accion" id="btnConfirmarAprobar"
                                    data-solicitud-id="{{ $solicitud->id_solicitud }}" disabled>
                                    <i class="fas fa-check me-1"></i> Confirmar aprobación y turnar
                                </button>
                                <button type="button" class="btn-cancelar-accion" id="btnCancelarAprobar">
                                    <i class="fas fa-times me-1"></i> Cancelar
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Panel de rechazo --}}
                    <div class="rechazo-panel" id="rechazoPanel">
                        <div class="rechazo-header">
                            <i class="fas fa-exclamation-triangle me-1"></i> Motivo del rechazo
                        </div>
                        <div class="rechazo-body">
                            <textarea id="motivoRechazo" class="rechazo-textarea"
                                placeholder="Describe el motivo por el cual se rechaza esta solicitud..."></textarea>
                            <div class="rechazo-actions">
                                <button type="button" class="btn-confirmar-accion btn-confirmar-rechazo"
                                    id="btnConfirmarRechazo" data-solicitud-id="{{ $solicitud->id_solicitud }}">
                                    <i class="fas fa-check me-1"></i> Confirmar rechazo
                                </button>
                                <button type="button" class="btn-cancelar-accion" id="btnCancelarRechazo">
                                    <i class="fas fa-times me-1"></i> Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Si ya está resuelta --}}
        @if ($solicitud->estatus_solicitud !== 0)
            <div class="detalles-card">
                <div class="detalles-card-body text-center">
                    <a href="{{ route('solicitudes.index') }}" class="btn-accion btn-regresar">
                        <i class="fas fa-arrow-left"></i> Regresar al listado
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="{{ asset('js/solicitudes/verDetalles.js') }}"></script>
    <script>
        function verArchivo(ruta) {
            if (!ruta) {
                Swal.fire({
                    icon: 'info',
                    title: 'Sin archivo',
                    text: 'No hay un archivo disponible para este requisito.',
                    confirmButtonColor: '#601028',
                });
                return;
            }
            window.open('{{ route('documento.ver') }}?ruta=' + encodeURIComponent(ruta), '_blank');
        }
    </script>
@endsection
