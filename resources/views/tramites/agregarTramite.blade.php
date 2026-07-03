@extends('layouts.admin')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/tramites/agregarTramite.css') }}">

    <div class="main-container">
        <div class="page-header">
            <div class="header-content">
                <img src="{{ asset('images/escudoBlanco.png') }}" alt="Escudo de Salamanca" class="header-escudo">
                <div class="header-main">
                    <h1 class="page-title">Agregar trámite</h1>
                    <p class="page-subtitle">Registra un nuevo trámite y asígnalo a una dependencia.</p>
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
                    <i class="fas fa-file-alt"></i>
                </div>
                <div>
                    <p class="card-form-header-title">Datos del trámite</p>
                    <p class="card-form-header-sub">Todos los campos son obligatorios.</p>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('registrarTramite') }}" id="form-agregar-tramite" novalidate>
                    @csrf

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nombre" class="form-label">
                                <i class="fas fa-font me-1"></i>Nombre del trámite
                            </label>
                            <input type="text" name="nombre" id="nombre"
                                class="form-control @error('nombre') is-invalid @enderror"
                                value="{{ old('nombre') }}" maxlength="255" required
                                autocomplete="off" placeholder="Ej. Licencia de funcionamiento">
                            <div class="field-footer">
                                <span class="field-message">
                                    @if ($errors->has('nombre'))
                                        <span class="field-error"><i class="fas fa-circle-exclamation me-1"></i>{{ $errors->first('nombre') }}</span>
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
                                class="form-control @error('descripcion') is-invalid @enderror"
                                required autocomplete="off"
                                placeholder="Describe brevemente en qué consiste el trámite">{{ old('descripcion') }}</textarea>
                            <div class="field-footer">
                                <span class="field-message">
                                    @if ($errors->has('descripcion'))
                                        <span class="field-error"><i class="fas fa-circle-exclamation me-1"></i>{{ $errors->first('descripcion') }}</span>
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
                                required>
                                <option value="" disabled selected>Cargando dependencias...</option>
                            </select>
                            <div class="field-footer">
                                <span class="field-message">
                                    @if ($errors->has('fk_dependencia'))
                                        <span class="field-error"><i class="fas fa-circle-exclamation me-1"></i>{{ $errors->first('fk_dependencia') }}</span>
                                    @else
                                        <span class="field-hint">Seleccione la dependencia a la que pertenece</span>
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="precio" class="form-label">
                                <i class="fas fa-dollar-sign me-1"></i>Precio del trámite
                            </label>
                            <input type="number" name="precio" id="precio"
                                class="form-control @error('precio') is-invalid @enderror"
                                value="{{ old('precio') }}" step="0.01" min="0" max="99999999.99"
                                required autocomplete="off" placeholder="Ej. 250.00">
                            <div class="field-footer">
                                <span class="field-message">
                                    @if ($errors->has('precio'))
                                        <span class="field-error"><i class="fas fa-circle-exclamation me-1"></i>{{ $errors->first('precio') }}</span>
                                    @else
                                        <span class="field-hint">Monto en moneda nacional. Mayor o igual a 0.</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('indexTramites') }}" class="btn-accion btn-regresar">
                            <i class="fas fa-arrow-left"></i>Regresar
                        </a>
                        <button type="submit" class="btn-accion btn-guardar" id="btn-guardar-tramite">
                            <i class="fas fa-floppy-disk"></i>Guardar trámite
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
        };
    </script>
    <script src="{{ asset('js/tramites/agregarTramite.js') }}"></script>
@endsection
