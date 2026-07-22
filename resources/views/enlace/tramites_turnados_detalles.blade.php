@extends('layouts.enlace')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/solicitudes/verDetalles.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .estado-atendida {
            background: #dff7e9;
            color: #0d7d3d;
            border: 1px solid #a8e6c1;
        }

        /* Upload zone */
        .upload-zone {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 2rem 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #f8fafc;
        }

        .upload-zone:hover,
        .upload-zone.dragover {
            border-color: #1e5c50;
            background: #edf7f4;
        }

        .upload-zone.has-file {
            border-style: solid;
            border-color: #1e5c50;
            background: #f0faf4;
            padding: 1rem 1.5rem;
        }

        .upload-zone-icon {
            font-size: 2.5rem;
            color: #94a3b8;
            margin-bottom: 0.5rem;
        }

        .upload-zone.has-file .upload-zone-icon {
            color: #1e5c50;
            font-size: 1.5rem;
            margin-bottom: 0;
        }

        .upload-zone-text {
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .upload-zone.has-file .upload-zone-text {
            font-size: 0.85rem;
        }

        .upload-zone input[type="file"] {
            display: none;
        }

        .upload-file-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-top: 0.5rem;
            flex-wrap: wrap;
        }

        .upload-file-name {
            font-size: 0.85rem;
            font-weight: 600;
            color: #1e5c50;
            background: #fff;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            border: 1px solid #d1ede6;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .upload-file-actions {
            display: flex;
            gap: 0.4rem;
        }

        .btn-icon-sm {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s ease;
            color: #64748b;
            font-size: 0.85rem;
        }

        .btn-icon-sm:hover {
            border-color: #94a3b8;
            background: #f1f5f9;
        }

        .btn-icon-sm.btn-change:hover {
            border-color: #f59e0b;
            color: #f59e0b;
        }

        .btn-icon-sm.btn-remove:hover {
            border-color: #ef4444;
            color: #ef4444;
        }

        .upload-zone-browse {
            color: #1e5c50;
            font-weight: 700;
            text-decoration: underline;
            cursor: pointer;
        }

        .aprobacion-panel {
            margin-top: 1.2rem;
            border: 2px solid #1e5c50;
            border-radius: var(--radius-md);
            overflow: hidden;
            display: none;
        }

        .aprobacion-panel.mostrar {
            display: block;
        }

        .aprobacion-header {
            background: #1e5c50;
            color: #fff;
            padding: 0.75rem 1.2rem;
            font-weight: 700;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .aprobacion-body {
            padding: 1.2rem;
            background: #f0faf4;
        }

        .resolucion-textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: "Montserrat", sans-serif;
            font-size: 0.88rem;
            resize: vertical;
            min-height: 80px;
            transition: all 0.2s ease;
            background: var(--bg-surface);
        }

        .resolucion-textarea:focus {
            outline: none;
            border-color: #1e5c50;
            box-shadow: 0 0 0 3px rgba(30, 92, 80, 0.12);
        }

        .resolucion-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1rem;
            justify-content: flex-end;
            flex-wrap: wrap;
        }
    </style>
@endsection

@section('content')
    <div class="detalles-container">

        {{-- Header --}}
        <div class="detalles-header">
            <div class="detalles-header-icon">
                <i class="fas fa-file-lines"></i>
            </div>
            <div class="detalles-header-text">
                <h1>Detalles de la solicitud #{{ $solicitud->id_solicitud }}</h1>
                <p>Información completa de la solicitud turnada y documentos presentados</p>
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
                                        class="fas fa-exchange-alt me-1"></i>Turnado</span>
                            @elseif ($solicitud->estatus_solicitud === 2)
                                <span class="estado-badge estado-rechazada"><i
                                        class="fas fa-times-circle me-1"></i>Rechazada</span>
                            @elseif ($solicitud->estatus_solicitud === 3)
                                <span class="estado-badge estado-atendida"><i
                                        class="fas fa-check-circle me-1"></i>Atendida</span>
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

        {{-- Card: Acciones (solo si está turnada) --}}
        @if ($solicitud->estatus_solicitud === 1)
            <div class="detalles-card">
                <div class="detalles-card-header">
                    <i class="fas fa-gavel"></i> Resolver solicitud
                </div>
                <div class="detalles-card-body">
                    <div class="acciones-container">
                        <button type="button" class="btn-accion btn-aprobar" id="btnAprobar">
                            <i class="fas fa-check-circle"></i> Aprobar y pagar
                        </button>
                        <button type="button" class="btn-accion btn-rechazar" id="btnRechazar">
                            <i class="fas fa-times-circle"></i> Rechazar
                        </button>
                        <a href="{{ route('enlace.tramitesTurnados') }}" class="btn-accion btn-regresar">
                            <i class="fas fa-arrow-left"></i> Regresar
                        </a>
                    </div>

                    {{-- Panel de aprobación con subida de archivo --}}
                    <div class="aprobacion-panel" id="aprobacionPanel">
                        <div class="aprobacion-header">
                            <i class="fas fa-file-circle-check me-1"></i> Aprobar trámite — Documento de resolución
                        </div>
                        <div class="aprobacion-body">
                            {{-- Nota opcional --}}
                            <div class="mb-3">
                                <label for="resolucionNota" class="form-label fw-bold"
                                    style="font-size:0.82rem;color:#1e5c50;">
                                    <i class="fas fa-sticky-note me-1"></i> Nota de resolución (opcional)
                                </label>
                                <textarea id="resolucionNota" class="resolucion-textarea"
                                    placeholder="Agrega una nota o comentario sobre la resolución...">{{ $resolucion?->resolucion_solicitud && !str_starts_with($resolucion->resolucion_solicitud, 'Rechazado:') ? $resolucion->resolucion_solicitud : '' }}</textarea>
                            </div>

                            {{-- Zona de subida de archivo --}}
                            <label class="form-label fw-bold" style="font-size:0.82rem;color:#1e5c50;">
                                <i class="fas fa-file-pdf me-1"></i> Documento de resolución (PDF, JPG, PNG — máx. 10 MB)
                            </label>
                            <div class="upload-zone" id="uploadZone">
                                <input type="file" id="fileInput" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="upload-zone-icon" id="uploadIcon">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                </div>
                                <div class="upload-zone-text" id="uploadText">
                                    <span class="upload-zone-browse">Browse File to upload!</span>
                                </div>
                                <div class="upload-file-info" id="uploadFileInfo" style="display:none;">
                                    <span class="upload-file-name" id="fileNameDisplay">
                                        <i class="fa-regular fa-file-pdf"></i>
                                        <span id="fileNameText"></span>
                                    </span>
                                    <span class="upload-file-actions">
                                        <button type="button" class="btn-icon-sm btn-change" id="btnChangeFile"
                                            title="Cambiar archivo">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button type="button" class="btn-icon-sm btn-remove" id="btnRemoveFile"
                                            title="Quitar archivo">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <div id="fileError" class="text-danger mt-1" style="font-size:0.8rem;display:none;"></div>

                            <div class="resolucion-actions">
                                <button type="button" class="btn-confirmar-accion" id="btnConfirmarAprobar"
                                    style="background:#1e5c50;color:#fff;">
                                    <i class="fas fa-check me-1"></i> Confirmar aprobación
                                </button>
                                <button type="button" class="btn-cancelar-accion" id="btnCancelarAprobar">
                                    <i class="fas fa-times me-1"></i> Cancelar
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Panel de rechazo --}}
                    <div class="rechazo-panel" id="rechazoPanel" style="display: none;">
                        <div class="rechazo-header">
                            <i class="fas fa-exclamation-triangle me-1"></i> Motivo del rechazo
                        </div>
                        <div class="rechazo-body">
                            <textarea id="motivoRechazo" class="rechazo-textarea"
                                placeholder="Describe el motivo por el cual se rechaza esta solicitud..."></textarea>
                            <div class="rechazo-actions">
                                <button type="button" class="btn-confirmar-accion btn-confirmar-rechazo"
                                    id="btnConfirmarRechazo" data-turnado-id="{{ $turnado->id_turnado }}">
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
        @if ($solicitud->estatus_solicitud !== 1)
            {{-- Mostrar resolución si existe --}}
            @if (isset($resolucion) && $resolucion)
                <div class="detalles-card">
                    <div class="detalles-card-header">
                        <i class="fas fa-file-circle-check"></i> Resolución
                    </div>
                    <div class="detalles-card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label"><i class="fas fa-sticky-note me-1"></i> Nota de resolución</span>
                                <span class="info-value">{{ $resolucion->resolucion_solicitud }}</span>
                            </div>
                            @if ($resolucion->documento_resolucion)
                                <div class="info-item full-width">
                                    <span class="info-label"><i class="fas fa-file-pdf me-1"></i> Documento de
                                        resolución</span>
                                    <span class="info-value" style="display:flex;align-items:center;gap:0.75rem;">
                                        <i class="fa-regular fa-file-pdf" style="font-size:1.2rem;color:#c62828;"></i>
                                        {{ basename($resolucion->documento_resolucion) }}
                                        <button type="button" class="btn-ver-archivo" title="Ver documento"
                                            onclick="verArchivo('{{ $resolucion->documento_resolucion }}')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
            <div class="detalles-card">
                <div class="detalles-card-body text-center">
                    <a href="{{ route('enlace.tramitesTurnados') }}" class="btn-accion btn-regresar">
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
    <script>
        window.enlaceDetallesRoutes = {
            aprobar: "{{ route('enlace.tramitesTurnadosAprobar', ['id' => $turnado->id_turnado]) }}",
            rechazar: "{{ route('enlace.tramitesTurnadosRechazar', ['id' => $turnado->id_turnado]) }}",
            verDocumento: "{{ route('documento.ver') }}",
            listado: "{{ route('enlace.tramitesTurnados') }}",
        };

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
            window.open(window.enlaceDetallesRoutes.verDocumento + '?ruta=' + encodeURIComponent(ruta), '_blank');
        }
    </script>
    <script src="{{ asset('js/enlace/tramites_turnados_detalles.js') }}"></script>
@endsection
