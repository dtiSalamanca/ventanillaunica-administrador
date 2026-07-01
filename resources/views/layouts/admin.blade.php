<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" />

    <title>Sistema de Administración de la Ventanilla Única | Salamanca, Guanajuato</title>
    <script>
        (function() {
            const themeStorageKey = 'admin-theme';
            const legacyThemeStorageKey = 'theme';
            const themeVariablesStorageKey = 'admin-theme-vars';
            const rootElement = document.documentElement;

            try {
                const currentTheme = localStorage.getItem(themeStorageKey) || localStorage.getItem(
                    legacyThemeStorageKey) || 'negro';
                rootElement.setAttribute('data-admin-theme', currentTheme);

                const storedVariables = localStorage.getItem(themeVariablesStorageKey);
                if (!storedVariables) {
                    return;
                }

                const parsedVariables = JSON.parse(storedVariables);
                if (!parsedVariables || typeof parsedVariables !== 'object') {
                    return;
                }

                Object.keys(parsedVariables).forEach((cssVariable) => {
                    if (!cssVariable.startsWith('--theme-')) {
                        return;
                    }

                    rootElement.style.setProperty(cssVariable, parsedVariables[cssVariable]);
                });
            } catch {}
        })();
    </script>

    <!-- Hojas de estilo -->
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;900&family=Roboto:wght@400;500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link href="{{ asset('css/layouts/admin.css') }}" rel="stylesheet">
    @yield('css')

</head>

<body class="sb-nav-fixed">
    <!-- Cortinilla de transición -->
    <div id="pageLoader" class="page-loader" aria-hidden="true">
        <div class="page-loader-content">
            <img src="{{ asset('images/escudoBlanco.png') }}" alt="" class="page-loader-logo">
            <h2 class="page-loader-title">Ventanilla Única</h2>
            <p class="page-loader-subtitle">Sistema de gestión de la ventanilla única de Salamanca, Guanajuato.</p>
            <div class="page-loader-spinner"></div>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark">
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" type="button">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Menú usuario -->
        <ul class="navbar-nav ms-auto me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                    onclick="toggleUserDropdown(event)">
                    <i class="fas fa-user"></i> <span class="ms-2">{{ auth()->user()?->name ?? '' }}@if (auth()->user()?->username)
                            ({{ auth()->user()->username }})
                        @endif
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown" id="userDropdownMenu">
                    <li class="user-dropdown-header">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-info">
                            <div class="user-name">{{ auth()->user()?->name ?? 'Usuario' }}@if (auth()->user()?->username)
                                    ({{ auth()->user()->username }})
                                @endif
                            </div>
                            @if (auth()->user()?->email)
                                <div class="user-email">{{ auth()->user()->email }}</div>
                            @endif
                        </div>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item" href="#"
                            onclick="event.preventDefault(); abrirPersonalizacionColores();">
                            <i class="fas fa-palette me-2"></i><span class="highlight-text">Cambiar colores de la
                                interfaz</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); confirmarCierreSesion();">
                            <i class="fas fa-sign-out-alt me-2"></i><span
                                class="highlight-text">{{ __('Cerrar sesión') }}</span>
                        </a>
                    </li>
                </ul>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
    </nav>

    <div id="layoutSidenav">
        <!-- Menú lateral -->
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-logo">
                    <img src="{{ asset('images/escudoBlanco.png') }}">
                </div>

                <div class="sb-sidenav-header">
                    <h1>Sistema de Administración de la Ventanilla Única</h1>
                </div>

                <div class="sb-sidenav-menu">
                    <div class="sb-sidenav-search">
                        <label for="sidebarSectionSearch" class="visually-hidden">Buscar</label>
                        <div class="sb-sidenav-search-box">
                            <i class="fas fa-search" aria-hidden="true"></i>
                            <input type="text" id="sidebarSectionSearch" class="sb-sidenav-search-input"
                                placeholder="Buscar..." autocomplete="off">
                            <button type="button" class="sb-sidenav-search-clear" id="sidebarSectionSearchClear"
                                aria-label="Limpiar búsqueda">
                                <i class="fas fa-times" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                    <div class="nav">
                        <!-- Inicio -->
                        <a class="nav-link active {{ request()->routeIs('home') ? 'active-current' : '' }}"
                            href="{{ route('home') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-house"></i></div>
                            Inicio
                        </a>

                        <div class="sb-sidenav-menu-heading">
                            <i class="fas fa-list-check me-2"></i>Catálogos
                        </div>

                        <a class="nav-link active {{ request()->routeIs('indexDependencias') ? 'active-current' : '' }}"
                            href="{{ route('indexDependencias') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-building-columns"></i></div>
                            Dependencias
                        </a>

                        <a class="nav-link active {{ request()->routeIs('indexTramites') ? 'active-current' : '' }}"
                            href="{{ route('indexTramites') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-file-lines"></i></div>
                            Trámites
                        </a>


                        <a class="nav-link active {{ request()->routeIs('indexRequisitos') ? 'active-current' : '' }}"
                            href="{{ route('indexRequisitos') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-clipboard-list"></i></div>
                            Requisitos
                        </a>

                        <a class="nav-link active {{ request()->routeIs('indexDocumentosPersonales') ? 'active-current' : '' }}"
                            href="{{ route('indexDocumentosPersonales') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-clipboard-list"></i></div>
                            Documentos Personales
                        </a>

                        <div class="sb-sidenav-menu-heading">
                            <i class="fas fa-list-check me-2"></i>Configuración
                        </div>

                        <a class="nav-link active {{ request()->routeIs('indexUsuarios') ? 'active-current' : '' }}"
                            href="{{ route('indexUsuarios') }}">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-users"></i></div>
                            Usuarios
                        </a>


                    </div>
                </div>
            </nav>
        </div>

        <!-- Contenido principal -->
        <div id="layoutSidenav_content">
            <main>
                @yield('content')
            </main>

            <!-- Pie de página -->
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">&copy; {{ date('Y') }} Salamanca, Guanajuato</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @php
        $adminConfig = [
            'csrfToken' => csrf_token(),
            'flashSuccess' => session('success', ''),
        ];
    @endphp
    <script>
        window.adminConfig = @json($adminConfig);
    </script>
    @include('modalColores')
    <script src="{{ asset('js/layouts/admin.js') }}"></script>

    @yield('scripts')
    @stack('scripts')

</body>

</html>
