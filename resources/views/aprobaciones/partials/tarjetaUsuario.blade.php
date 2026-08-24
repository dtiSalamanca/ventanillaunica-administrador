@php
    $estatusInfo = [
        \App\Models\tblDocumentoPersonal::ESTATUS_APROBADO => [
            'label' => 'Aprobado',
            'class' => 'badge-estatus-aprobado',
        ],
        \App\Models\tblDocumentoPersonal::ESTATUS_RECHAZADO => [
            'label' => 'Rechazado',
            'class' => 'badge-estatus-rechazado',
        ],
    ];
@endphp

<div class="usuario-card">
    <div class="usuario-card-header">
        <div class="usuario-avatar">
            <i class="fas fa-user"></i>
        </div>
        <div class="usuario-info">
            <div class="usuario-nombre">{{ $usuario->name }}</div>
            <div class="usuario-email">{{ $usuario->email }}</div>
        </div>
        @if ($pendiente)
            <span class="badge-pendientes">{{ $usuario->documentosPersonales->count() }} pendiente(s)</span>
        @else
            <span class="badge-total">{{ $usuario->documentosPersonales->count() }} documento(s)</span>
        @endif
    </div>

    <div class="accordion usuario-accordion" id="accordion-usuario-{{ $prefijo }}-{{ $usuario->id }}">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapse-usuario-{{ $prefijo }}-{{ $usuario->id }}" aria-expanded="false"
                    aria-controls="collapse-usuario-{{ $prefijo }}-{{ $usuario->id }}">
                    <i class="fa-solid fa-folder-open me-2"></i>
                    {{ $pendiente ? 'Ver documentos pendientes' : 'Ver documentos revisados' }}
                </button>
            </h2>
            <div id="collapse-usuario-{{ $prefijo }}-{{ $usuario->id }}" class="accordion-collapse collapse"
                data-bs-parent="#accordion-usuario-{{ $prefijo }}-{{ $usuario->id }}">
                <div class="accordion-body">
                    @foreach ($usuario->documentosPersonales as $documento)
                        @php
                            $docPersonalAprobado =
                                $documento->estatus_documento === \App\Models\tblDocumentoPersonal::ESTATUS_APROBADO;
                            $fechaVencimientoDoc = $docPersonalAprobado ? $documento->fechaVencimiento() : null;
                        @endphp
                        <div class="documento-item">
                            <div class="documento-info">
                                <div class="documento-nombre">{{ $documento->catalogoDocumento->nombre_documento }}
                                </div>
                                <div class="documento-fecha">
                                    @if ($documento->fecha_aprobacion)
                                        <i class="fa-solid fa-circle-check me-1"></i>Aprobado el
                                        {{ $documento->fecha_aprobacion->format('d/m/Y') }}
                                    @else
                                        <i class="fa-regular fa-calendar me-1"></i>Cargado el
                                        {{ $documento->fecha_registro->format('d/m/Y') }}
                                    @endif
                                </div>
                                @if ($docPersonalAprobado && $fechaVencimientoDoc)
                                    @if ($documento->estaExpirado())
                                        <span class="doc-vigencia doc-vigencia--vencido"><i
                                                class="fa-solid fa-triangle-exclamation me-1"></i>Vencido
                                            ({{ $fechaVencimientoDoc->format('d/m/Y') }})</span>
                                    @elseif ($documento->estaPorVencer())
                                        <span class="doc-vigencia doc-vigencia--por-vencer"><i
                                                class="fa-solid fa-hourglass-half me-1"></i>Por vencer
                                            ({{ $fechaVencimientoDoc->format('d/m/Y') }})</span>
                                    @else
                                        <span class="doc-vigencia doc-vigencia--vigente"><i
                                                class="fa-solid fa-check me-1"></i>Vence:
                                            {{ $fechaVencimientoDoc->format('d/m/Y') }}</span>
                                    @endif
                                @endif
                                @if ($documento->estatus_documento === \App\Models\tblDocumentoPersonal::ESTATUS_RECHAZADO && $documento->motivo_rechazo)
                                    <div class="documento-motivo-rechazo" title="{{ $documento->motivo_rechazo }}">
                                        <i class="fa-solid fa-circle-info"></i>
                                        {{ $documento->motivo_rechazo }}
                                    </div>
                                @endif
                            </div>
                            @if ($pendiente)
                                <div class="documento-acciones">
                                    <a href="{{ route('visualizarDocumentoPersonal', $documento->id_documento) }}"
                                        class="btn-visualizar" target="_blank" rel="noopener"
                                        title="Visualizar documento">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn-aprobar btn-aprobar-documento"
                                        data-id="{{ $documento->id_documento }}" title="Aprobar">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="btn-rechazar btn-rechazar-documento"
                                        data-id="{{ $documento->id_documento }}"
                                        data-documento="{{ $documento->catalogoDocumento->nombre_documento }}"
                                        data-nombre-usuario="{{ $usuario->name }}" title="Rechazar">
                                        <i class="fas fa-xmark"></i>
                                    </button>
                                </div>
                            @else
                                <div class="documento-acciones">
                                    <a href="{{ route('visualizarDocumentoPersonal', $documento->id_documento) }}"
                                        class="btn-visualizar" target="_blank" rel="noopener"
                                        title="Visualizar documento">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <span
                                        class="badge-estatus {{ $estatusInfo[$documento->estatus_documento]['class'] }}">
                                        {{ $estatusInfo[$documento->estatus_documento]['label'] }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
