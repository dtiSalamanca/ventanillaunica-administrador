@extends('layouts.admin')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/predios/editarDocumentoPredio.css') }}">

    <div class="main-container">
        <div class="page-header">
            <div class="header-content">
                <img src="{{ asset('images/escudoBlanco.png') }}" alt="Escudo de Salamanca" class="header-escudo">
                <div class="header-main">
                    <h1 class="page-title">Editar documento de predio</h1>
                    <p class="page-subtitle">Modifica los datos del documento de predio seleccionado.</p>
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
                    <i class="fas fa-file"></i>
                </div>
                <div>
                    <p class="card-form-header-title">Datos del documento de predio</p>
                    <p class="card-form-header-sub">Todos los campos son obligatorios.</p>
                </div>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('actualizarDocumentoPredio', $documentoPredio) }}" id="form-editar-documento-predio" novalidate>
                    @csrf

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nombre_documento" class="form-label">
                                <i class="fas fa-font me-1"></i>Nombre del documento
                            </label>
                            <input type="text" name="nombre_documento" id="nombre_documento"
                                class="form-control @error('nombre_documento') is-invalid @enderror"
                                value="{{ old('nombre_documento', $documentoPredio->nombre_documento) }}" maxlength="255" required
                                autocomplete="off" placeholder="Ej. Escritura del predio">
                            <div class="field-footer">
                                <span class="field-message">
                                    @if ($errors->has('nombre_documento'))
                                        <span class="field-error"><i class="fas fa-circle-exclamation me-1"></i>{{ $errors->first('nombre_documento') }}</span>
                                    @else
                                        <span class="field-hint">Debe ser un nombre único</span>
                                    @endif
                                </span>
                                <span class="char-counter" id="counter-nombre_documento"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="vigencia_meses" class="form-label">
                                <i class="fas fa-calendar-alt me-1"></i>Vigencia (meses)
                            </label>
                            <input type="number" name="vigencia_meses" id="vigencia_meses"
                                class="form-control @error('vigencia_meses') is-invalid @enderror"
                                value="{{ old('vigencia_meses', $documentoPredio->vigencia_meses) }}" min="1" required
                                autocomplete="off" placeholder="Ej. 12">
                            <div class="field-footer">
                                <span class="field-message">
                                    @if ($errors->has('vigencia_meses'))
                                        <span class="field-error"><i class="fas fa-circle-exclamation me-1"></i>{{ $errors->first('vigencia_meses') }}</span>
                                    @else
                                        <span class="field-hint">Número entero mayor a 0</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('indexPredios') }}" class="btn-accion btn-regresar">
                            <i class="fas fa-arrow-left"></i>Regresar
                        </a>
                        <button type="submit" class="btn-accion btn-guardar" id="btn-actualizar-documento-predio">
                            <i class="fas fa-floppy-disk"></i>Actualizar documento de predio
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/predios/editarDocumentoPredio.js') }}"></script>
@endsection