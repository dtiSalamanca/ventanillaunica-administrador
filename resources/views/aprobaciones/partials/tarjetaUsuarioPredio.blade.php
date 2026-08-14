@php
    $estatusInfo = [
        \App\Models\Predio::ESTATUS_EN_REVISION => ['label' => 'En revisión', 'class' => 'badge-estatus-revision'],
        \App\Models\Predio::ESTATUS_APROBADO => ['label' => 'Aprobado', 'class' => 'badge-estatus-aprobado'],
        \App\Models\Predio::ESTATUS_RECHAZADO => ['label' => 'Rechazado', 'class' => 'badge-estatus-rechazado'],
    ];

    $prediosPendientes = $usuario->predios
        ->filter(function ($predio) {
            return in_array($predio->estatus_predio, [
                \App\Models\Predio::ESTATUS_EN_REVISION,
                \App\Models\Predio::ESTATUS_POR_REVISAR,
            ]) || $predio->documentos->contains('estatus_documento', \App\Models\DocumentoPredio::ESTATUS_EN_REVISION);
        })
        ->count();
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
            <span class="badge-pendientes">{{ $prediosPendientes }} predio(s) pendiente(s)</span>
        @else
            <span class="badge-total">{{ $usuario->predios->count() }} predio(s)</span>
        @endif
    </div>

    <div class="accordion usuario-accordion" id="accordion-usuario-{{ $prefijo }}-{{ $usuario->id }}">
        @foreach ($usuario->predios as $predio)
            @php
                $tieneDocumentoPendiente = $predio->documentos->contains(
                    'estatus_documento',
                    \App\Models\DocumentoPredio::ESTATUS_EN_REVISION,
                );
                // Predio en estado pendiente (por revisar o en revisión): solo estos
                // tienen sentido de consultarse contra el sistema de predial.
                $predioEnPendiente = in_array($predio->estatus_predio, [
                    \App\Models\Predio::ESTATUS_EN_REVISION,
                    \App\Models\Predio::ESTATUS_POR_REVISAR,
                ]);
                $predioPendiente = $predioEnPendiente || $tieneDocumentoPendiente;
            @endphp
            <div class="accordion-item predio-item @if ($predioPendiente) predio-pendiente @endif">
                <h2 class="accordion-header predio-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse-predio-{{ $prefijo }}-{{ $predio->id_predio }}"
                        aria-expanded="false"
                        aria-controls="collapse-predio-{{ $prefijo }}-{{ $predio->id_predio }}">
                        <i class="fa-solid fa-map-location-dot me-2"></i>
                        Predio: {{ $predio->clave_predio }}
                    </button>

                    <div class="predio-acciones">
                        {{-- La consulta (lupa) solo se muestra para predios pendientes que aún
                             no se han consultado (CONSULTADO_SIN_CONSULTAR). Al consultar, si la
                             cuenta existe el predio se aprueba automáticamente. --}}
                        @if ($predioEnPendiente && $predio->consultado === \App\Models\Predio::CONSULTADO_SIN_CONSULTAR)
                            <button type="button" class="btn-consultar btn-buscar-predio"
                                data-id="{{ $predio->clave_predio }}"
                                title="Consultar existencia del predio en el sistema de predial">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        @endif

                        @if (in_array($predio->estatus_predio, [\App\Models\Predio::ESTATUS_APROBADO, \App\Models\Predio::ESTATUS_RECHAZADO]))
                            <span class="badge-estatus {{ $estatusInfo[$predio->estatus_predio]['class'] }}">
                                {{ $estatusInfo[$predio->estatus_predio]['label'] }}
                            </span>
                        @else
                            {{-- Pendiente: aún no se ha validado la existencia, solo se puede rechazar --}}
                            <button type="button" class="btn-rechazar btn-rechazar-predio"
                                data-id="{{ $predio->id_predio }}" data-clave="{{ $predio->clave_predio }}"
                                data-nombre-usuario="{{ $usuario->name }}" title="Rechazar predio">
                                <i class="fas fa-xmark"></i>
                            </button>
                        @endif
                    </div>
                </h2>

                <div id="collapse-predio-{{ $prefijo }}-{{ $predio->id_predio }}"
                    class="accordion-collapse collapse"
                    data-bs-parent="#accordion-usuario-{{ $prefijo }}-{{ $usuario->id }}">
                    <div class="accordion-body">
                        @if ($predioEnPendiente && $predio->consultado === \App\Models\Predio::CONSULTADO_SIN_CONSULTAR)
                            <div class="predio-consulta">
                                <button type="button" class="btn-consultar-predio btn-buscar-predio"
                                    data-id="{{ $predio->clave_predio }}"
                                    title="Consultar si la cuenta predial existe en el sistema de predial">
                                    <i class="fa-solid fa-magnifying-glass-chart"></i>
                                    Consultar existencia del predio
                                </button>
                            </div>
                        @endif
                        @if ($predio->estatus_predio === \App\Models\Predio::ESTATUS_RECHAZADO && $predio->motivo_rechazo)
                            <div class="documento-motivo-rechazo predio-motivo-rechazo"
                                title="{{ $predio->motivo_rechazo }}">
                                <i class="fa-solid fa-circle-info"></i>
                                {{ $predio->motivo_rechazo }}
                            </div>
                        @endif
                        @forelse ($predio->documentos as $documento)
                            <div class="documento-item">
                                <div class="documento-info">
                                    <div class="documento-nombre">{{ $documento->catalogoDocumento->nombre_documento }}
                                    </div>
                                    <div class="documento-fecha">{{ $documento->created_at->format('d/m/Y') }}</div>
                                    @if ($documento->estatus_documento === \App\Models\DocumentoPredio::ESTATUS_RECHAZADO && $documento->motivo_rechazo)
                                        <div class="documento-motivo-rechazo" title="{{ $documento->motivo_rechazo }}">
                                            <i class="fa-solid fa-circle-info"></i>
                                            {{ $documento->motivo_rechazo }}
                                        </div>
                                    @endif
                                </div>

                                <div class="documento-acciones">
                                    <a href="{{ route('visualizarDocumentoPredio', $documento->id_documento_predio) }}"
                                        class="btn-visualizar" target="_blank" rel="noopener"
                                        title="Visualizar documento">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if ($documento->estatus_documento === \App\Models\DocumentoPredio::ESTATUS_EN_REVISION)
                                        <button type="button" class="btn-aprobar btn-aprobar-documento-predio"
                                            data-id="{{ $documento->id_documento_predio }}" title="Aprobar">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn-rechazar btn-rechazar-documento-predio"
                                            data-id="{{ $documento->id_documento_predio }}"
                                            data-documento="{{ $documento->catalogoDocumento->nombre_documento }}"
                                            data-nombre-usuario="{{ $usuario->name }}" title="Rechazar">
                                            <i class="fas fa-xmark"></i>
                                        </button>
                                    @else
                                        <span
                                            class="badge-estatus {{ $estatusInfo[$documento->estatus_documento]['class'] }}">
                                            {{ $estatusInfo[$documento->estatus_documento]['label'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="predio-sin-documentos">Este predio no tiene documentos cargados.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
