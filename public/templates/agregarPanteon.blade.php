@extends('layouts.admin')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/panteones/agregarPanteon.css') }}">

    <div class="main-container">
        <div class="page-header">
            <div class="header-content">
                <img src="{{ asset('images/escudoBlanco.png') }}" alt="Escudo de Salamanca" class="header-escudo">
                <div class="header-main">
                    <h1 class="page-title">Agregar panteón</h1>
                    <p class="page-subtitle">Registra un nuevo panteón con su nombre y dirección.</p>
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
                    <i class="fas fa-landmark"></i>
                </div>
                <div>
                    <p class="card-form-header-title">Datos del panteón</p>
                    <p class="card-form-header-sub">Todos los campos son obligatorios.</p>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('registrarPanteon') }}" id="form-agregar-panteon" novalidate>
                    @csrf

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nombre_panteon" class="form-label">
                                <i class="fas fa-font me-1"></i>Nombre del panteón
                            </label>
                            <input type="text" name="nombre_panteon" id="nombre_panteon"
                                class="form-control @error('nombre_panteon') is-invalid @enderror"
                                value="{{ old('nombre_panteon') }}" maxlength="255" required
                                autocomplete="off" placeholder="Ej. Panteón Municipal Norte">
                            <div class="field-footer">
                                <span class="field-message">
                                    @if ($errors->has('nombre_panteon'))
                                        <span class="field-error"><i class="fas fa-circle-exclamation me-1"></i>{{ $errors->first('nombre_panteon') }}</span>
                                    @else
                                        <span class="field-hint">Debe ser un nombre único</span>
                                    @endif
                                </span>
                                <span class="char-counter" id="counter-nombre"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="direccion_panteon" class="form-label">
                                <i class="fas fa-map-location-dot me-1"></i>Dirección
                            </label>
                            <input type="text" name="direccion_panteon" id="direccion_panteon"
                                class="form-control @error('direccion_panteon') is-invalid @enderror"
                                value="{{ old('direccion_panteon') }}" maxlength="255" required
                                autocomplete="off" placeholder="Calle, colonia, municipio">
                            <div class="field-footer">
                                <span class="field-message">
                                    @if ($errors->has('direccion_panteon'))
                                        <span class="field-error"><i class="fas fa-circle-exclamation me-1"></i>{{ $errors->first('direccion_panteon') }}</span>
                                    @else
                                        <span class="field-hint">Ubicación completa del panteón</span>
                                    @endif
                                </span>
                                <span class="char-counter" id="counter-direccion"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('indexPanteones') }}" class="btn-accion btn-regresar">
                            <i class="fas fa-arrow-left"></i>Regresar
                        </a>
                        <button type="submit" class="btn-accion btn-guardar" id="btn-guardar-panteon">
                            <i class="fas fa-floppy-disk"></i>Guardar panteón
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/panteones/agregarPanteon.js') }}"></script>
@endsection
