@extends('layouts.admin')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/tramites/editarTramite.css') }}">

    <div class="main-container">
        <div class="page-header">
            <div class="header-content">
                <img src="{{ asset('images/escudoBlanco.png') }}" alt="Escudo de Salamanca" class="header-escudo">
                <div class="header-main">
                    <h1 class="page-title">Editar trámite</h1>
                    <p class="page-subtitle">Modifica los datos del trámite seleccionado.</p>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-times-circle me-2"></i>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-form-header">
                <div class="card-form-header-icon">
                    <i class="fas fa-pen-to-square"></i>
                </div>
                <div>
                    <p class="card-form-header-title">Datos del trámite</p>
                    <p class="card-form-header-sub">Todos los campos son obligatorios.</p>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('actualizarTramite', ['tramite' => $tramite->id_tramite]) }}"
                    id="form-editar-tramite" novalidate>
                    @csrf

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nombre" class="form-label">
                                <i class="fas fa-font me-1"></i>Nombre del trámite
                            </label>
                            <input type="text" name="nombre" id="nombre"
                                class="form-control @error('nombre') is-invalid @enderror"
                                value="{{ old('nombre', $tramite->nombre_tramite) }}" maxlength="255" required
                                autocomplete="off" placeholder="Ej. Licencia de funcionamiento">
                            <div class="field-footer">
                                <span class="field-message">
                                    @if ($errors->has('nombre'))
                                        <span class="field-error"><i
                                                class="fas fa-circle-exclamation me-1"></i>{{ $errors->first('nombre') }}</span>
                                    @else
                                        <span class="field-hint">Debe ser un nombre único</span>
                                    @endif
                                </span>
                                <span class="char-counter" id="counter-nombre"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="descripcion" class="form-label">
                                <i class="fas fa-align-left me-1"></i>Descripción del trámite
                            </label>
                            <textarea name="descripcion" id="descripcion" rows="4"
                                class="form-control @error('descripcion') is-invalid @enderror" required autocomplete="off"
                                placeholder="Describe brevemente en qué consiste el trámite">{{ old('descripcion', $tramite->descripcion_tramite) }}</textarea>
                            <div class="field-footer">
                                <span class="field-message">
                                    @if ($errors->has('descripcion'))
                                        <span class="field-error"><i
                                                class="fas fa-circle-exclamation me-1"></i>{{ $errors->first('descripcion') }}</span>
                                    @else
                                        <span class="field-hint">Explica brevemente el propósito del trámite</span>
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="fk_dependencia" class="form-label">
                                <i class="fas fa-building me-1"></i>Dependencia
                            </label>
                            <select name="fk_dependencia" id="fk_dependencia"
                                class="form-control @error('fk_dependencia') is-invalid @enderror"
                                data-selected="{{ old('fk_dependencia', $tramite->fk_dependencia) }}" required>
                                <option value="" disabled selected>Cargando dependencias...</option>
                            </select>
                            <div class="field-footer">
                                <span class="field-message">
                                    @if ($errors->has('fk_dependencia'))
                                        <span class="field-error"><i
                                                class="fas fa-circle-exclamation me-1"></i>{{ $errors->first('fk_dependencia') }}</span>
                                    @else
                                        <span class="field-hint">Seleccione la dependencia a la que pertenece</span>
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="fk_cri" class="form-label">
                                <i class="fas fa-building me-1"></i>Elegir cuenta contable del trámite
                            </label>
                            <select name="fk_cri" id="fk_cri" class="form-control @error('fk_cri') is-invalid @enderror"
                                data-selected="{{ old('fk_cri', $tramite->tramite_cri ?? '') }}" required>
                                <option value="" disabled selected>Cargando cuentas contables...</option>
                            </select>
                            <div class="field-footer">
                                <span class="field-message">
                                    @if ($errors->has('fk_cri'))
                                        <span class="field-error"><i
                                                class="fas fa-circle-exclamation me-1"></i>{{ $errors->first('fk_cri') }}</span>
                                    @else
                                        <span class="field-hint">Seleccione la cuenta contable a la que pertenece</span>
                                    @endif
                                </span>
                            </div>
                        </div>

                    </div>

                    <!-- Toggle: cobro por metro cuadrado -->
                    <div class="form-group form-group-toggle" id="form-group-cobra-m2">
                        <div class="form-check form-check-custom">
                            <input type="checkbox" class="form-check-input" id="cobra_por_m2" name="cobra_por_m2"
                                value="1" {{ old('cobra_por_m2', $tramite->cobra_por_m2) ? 'checked' : '' }}>
                            <label class="form-check-label" for="cobra_por_m2">
                                <i class="fas fa-ruler-combined me-1"></i>Se cobra por metro cuadrado (m²)
                            </label>
                        </div>
                        <div class="field-footer">
                            <span class="field-message">
                                <span class="field-hint">El precio del trámite no se captura aquí: lo capturará el
                                    enlace al atender cada solicitud.</span>
                            </span>
                        </div>
                        <div class="alert alert-info mt-2 mb-0 d-none" id="nota-precio-por-m2">
                            <i class="fas fa-info-circle me-2"></i>Este trámite se cobra por metro cuadrado.
                            El precio se definirá al momento de generar la orden de pago.
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('indexTramites') }}" class="btn-accion btn-regresar">
                            <i class="fas fa-arrow-left"></i>Regresar
                        </a>
                        <button type="submit" class="btn-accion btn-guardar" id="btn-actualizar-tramite">
                            <i class="fas fa-floppy-disk"></i>Actualizar trámite
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        window.dependenciasRoutes = {
            activas: "{{ route('getDependenciasActivas') }}",
            cuentasCri: "{{ route('ajax.ordenes-pago.consulta-cuentas-cri') }}",
        };
    </script>
    <script src="{{ asset('js/tramites/editarTramite.js') }}"></script>
@endsection
