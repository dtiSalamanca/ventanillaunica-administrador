@extends('layouts.admin')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/dependencias/editarDependencia.css') }}">

    <div class="main-container">
        <div class="page-header">
            <div class="header-content">
                <img src="{{ asset('images/escudoBlanco.png') }}" alt="Escudo de Salamanca" class="header-escudo">
                <div class="header-main">
                    <h1 class="page-title">Editar dependencia</h1>
                    <p class="page-subtitle">Modifica el nombre de la dependencia seleccionada.</p>
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
                    <i class="fas fa-building"></i>
                </div>
                <div>
                    <p class="card-form-header-title">Datos de la dependencia</p>
                    <p class="card-form-header-sub">Todos los campos son obligatorios.</p>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('actualizarDependencia', $dependencia) }}" id="form-editar-dependencia" novalidate>
                    @csrf

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nombre" class="form-label">
                                <i class="fas fa-font me-1"></i>Nombre de la dependencia
                            </label>
                            <input type="text" name="nombre" id="nombre"
                                class="form-control @error('nombre') is-invalid @enderror"
                                value="{{ old('nombre', $dependencia->nombre) }}" maxlength="255" required
                                autocomplete="off" placeholder="Ej. Dirección de Servicios Públicos">
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
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('indexDependencias') }}" class="btn-accion btn-regresar">
                            <i class="fas fa-arrow-left"></i>Regresar
                        </a>
                        <button type="submit" class="btn-accion btn-guardar" id="btn-actualizar-dependencia">
                            <i class="fas fa-floppy-disk"></i>Actualizar dependencia
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/dependencias/editarDependencia.js') }}"></script>
@endsection
