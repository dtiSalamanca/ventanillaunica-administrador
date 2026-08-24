@extends('layouts.login')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">

    @if (config('services.recaptcha_v3.enabled') && filled(config('services.recaptcha_v3.site_key')))
        <script
            src="https://www.google.com/recaptcha/api.js?render={{ urlencode((string) config('services.recaptcha_v3.site_key')) }}"
            async defer></script>
    @endif
@endsection

@section('content')
    <div class="auth-page">
        <div class="auth-grid">
            <!-- Columna izquierda: panel con el formulario -->
            <div class="auth-panel">
                <div class="auth-card">
                    <div class="auth-header">
                        <!-- <span class="auth-badge">Iniciar sesión</span> -->
                        <img src="{{ asset('images/escudoArma.png') }}" alt="Escudo de armas" class="auth-logo">
                        <h1 class="auth-title">SISTEMA DE ADMINISTRACIÓN DE LA VENTANILLA ÚNICA</h1>
                        <p class="auth-subtitle">Accede a la plataforma de administración de la ventanilla única de
                            Salamanca, Guanajuato.
                        </p>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">{{ session('status') }}</div>
                    @endif

                    @error('ad')
                        <div class="alert alert-danger" role="alert">{{ $message }}</div>
                    @enderror

                    @error('username')
                        <div class="alert alert-danger" role="alert">{{ $message }}</div>
                    @enderror

                    @error('password')
                        <div class="alert alert-danger" role="alert">{{ $message }}</div>
                    @enderror

                    @error('carta_compromiso')
                        <div class="firma-alert" role="alert">
                            <div class="firma-alert__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M12 2l8 4v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6l8-4z" />
                                    <path d="M9.5 12.5l2 2 3.5-4" />
                                </svg>
                            </div>
                            <div class="firma-alert__body">
                                <h3 class="firma-alert__title">Falta firmar tu carta compromiso</h3>
                                <p class="firma-alert__text">{{ $message }} Debes firmarla en el portal oficial antes de
                                    poder acceder al sistema.</p>
                                <a class="firma-alert__cta" href="https://cartascompromiso.salamanca.gob.mx"
                                    target="_blank" rel="noopener noreferrer">
                                    Ir a firmar mi carta compromiso
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M7 17L17 7M9 7h8v8" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @enderror

                    <div id="recaptchaErrorContainer"
                        class="alert alert-danger @unless ($errors->has('recaptcha_token')) is-hidden @endunless" role="alert">
                        <span id="recaptchaError">{{ $errors->first('recaptcha_token') }}</span>
                    </div>

                    <form id="loginForm" method="POST" action="{{ route('login') }}" novalidate
                        data-recaptcha-enabled='@json((bool) config('services.recaptcha_v3.enabled') && filled(config('services.recaptcha_v3.site_key')))'
                        data-recaptcha-site-key="{{ (string) config('services.recaptcha_v3.site_key') }}"
                        data-recaptcha-action="{{ (string) config('services.recaptcha_v3.action', 'login') }}">
                        @csrf

                        <!-- Campo oculto: Rol de Usuario (valor por defecto: administrador) -->
                        <input type="hidden" name="role" value="administrador">
                        <input id="recaptcha_token" type="hidden" name="recaptcha_token" value="">

                        <!-- Campo: Email o Usuario (label integrado) -->
                        <div class="form-field form-field--float">
                            <input id="username" type="text" name="username" value="{{ old('username') }}" required
                                autocomplete="username" autofocus class="input-control input-control--float" placeholder=" "
                                aria-describedby="usernameError" aria-invalid="false">
                            <label for="username" class="float-label">Usuario</label>
                            <small id="usernameError" class="error-text" role="alert"></small>
                        </div>

                        <!-- Campo: Contraseña (label integrado) -->
                        <div class="form-field form-field--float">
                            <input id="password" type="password" name="password" required minlength="6"
                                autocomplete="current-password" class="input-control input-control--float input-has-suffix"
                                placeholder=" " aria-describedby="passwordError capsHint" aria-invalid="false">
                            <label for="password" class="float-label">Contraseña</label>
                            <button type="button" id="togglePassword" class="input-toggle"
                                aria-label="Mostrar u ocultar contraseña">Ver</button>
                            <small id="passwordError" class="error-text" role="alert"></small>
                            <div id="capsHint" class="caps-hint">Bloq Mayús activado</div>
                        </div>

                        <!-- Fila: Recordarme y ¿Olvidaste tu contraseña? -->

                        <!-- Botón de envío -->
                        <div class="form-field form-field--submit">
                            <button type="submit" class="btn-primary-login">Iniciar sesión</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Columna derecha: ilustración a pantalla completa -->
            <div class="auth-illustration auth-illustration--login"></div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/auth/login.js') }}" defer></script>
@endsection
