@extends('layouts.admin')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin-home.css') }}">
@endsection

@section('content')
    <div class="admin-home-container">

        {{-- Hero --}}
        <section class="admin-hero-section">
            <div class="admin-hero-header">
                <div class="admin-hero-bg-pattern"></div>
                <h1 class="admin-hero-title">Panel de Administración</h1>
                <p class="admin-hero-subtitle">Sistema de Administración de la Ventanilla Única — H. Ayuntamiento de
                    Salamanca, Guanajuato</p>
            </div>
            <div class="admin-hero-body">
                <p class="admin-hero-welcome">
                    Bienvenido, <strong>{{ auth()->user()?->name ?? 'Administrador' }}</strong>
                </p>
                <p class="admin-hero-description">
                    Desde este panel puedes gestionar las solicitudes de trámites, aprobar documentos personales y de
                    predios, administrar los catálogos del sistema y gestionar los usuarios registrados.
                </p>
            </div>
        </section>

        {{-- Stats --}}
        <div class="admin-stats-grid">
            <div class="admin-stat-card">
                <div class="admin-stat-icon admin-stat-icon--secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                </div>
                <div class="admin-stat-info">
                    <div class="admin-stat-number">{{ $totalSolicitudes }}</div>
                    <p class="admin-stat-label">Solicitudes totales</p>
                    <a href="{{ route('solicitudes.index') }}" class="admin-stat-link">Ver solicitudes &rarr;</a>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-icon admin-stat-icon--warning">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <div class="admin-stat-info">
                    <div class="admin-stat-number">{{ $solicitudesPendientes }}</div>
                    <p class="admin-stat-label">Solicitudes pendientes</p>
                    <a href="{{ route('solicitudes.index') }}" class="admin-stat-link">Revisar &rarr;</a>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-icon admin-stat-icon--primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                </div>
                <div class="admin-stat-info">
                    <div class="admin-stat-number">{{ $documentosPendientes + $documentosPrediosPendientes }}</div>
                    <p class="admin-stat-label">Documentos por aprobar</p>
                    <a href="{{ route('indexAprobacionesDocumentosPersonales') }}" class="admin-stat-link">Ir a
                        aprobaciones &rarr;</a>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-icon admin-stat-icon--info">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <div class="admin-stat-info">
                    <div class="admin-stat-number">{{ $totalUsuarios }}</div>
                    <p class="admin-stat-label">Usuarios registrados</p>
                    <a href="{{ route('indexUsuarios') }}" class="admin-stat-link">Gestionar &rarr;</a>
                </div>
            </div>
        </div>

        {{-- Accesos rápidos --}}
        <h3 class="admin-section-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline>
                <path
                    d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z">
                </path>
            </svg>
            Accesos rápidos
        </h3>

        <div class="admin-features-grid">
            {{-- Solicitudes --}}
            <a href="{{ route('solicitudes.index') }}" class="admin-feature-card">
                <div class="admin-feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                </div>
                <h4 class="admin-feature-title">Solicitudes</h4>
                <p class="admin-feature-text">Revisa y da seguimiento a las solicitudes de trámites registradas por los
                    ciudadanos.</p>
            </a>

            {{-- Aprobación documentos personales --}}
            <a href="{{ route('indexAprobacionesDocumentosPersonales') }}" class="admin-feature-card">
                <div class="admin-feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="9 11 12 14 22 4"></polyline>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                    </svg>
                </div>
                <h4 class="admin-feature-title">Documentos personales</h4>
                <p class="admin-feature-text">Aprueba o rechaza los documentos personales que los ciudadanos han
                    subido al sistema.</p>
            </a>

            {{-- Aprobación predios --}}
            <a href="{{ route('indexAprobacionesPredios') }}" class="admin-feature-card">
                <div class="admin-feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                </div>
                <h4 class="admin-feature-title">Predios</h4>
                <p class="admin-feature-text">Administra la aprobación de documentos de predios registrados por los
                    ciudadanos.</p>
            </a>

            {{-- Dependencias --}}
            <a href="{{ route('indexDependencias') }}" class="admin-feature-card">
                <div class="admin-feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </div>
                <h4 class="admin-feature-title">Dependencias</h4>
                <p class="admin-feature-text">Gestiona las dependencias municipales asociadas a los trámites
                    disponibles.</p>
            </a>

            {{-- Trámites --}}
            <a href="{{ route('indexTramites') }}" class="admin-feature-card">
                <div class="admin-feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                </div>
                <h4 class="admin-feature-title">Trámites</h4>
                <p class="admin-feature-text">Administra el catálogo de trámites y asigna los requisitos
                    correspondientes.</p>
            </a>

            {{-- Requisitos --}}
            <a href="{{ route('indexRequisitos') }}" class="admin-feature-card">
                <div class="admin-feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <line x1="8" y1="6" x2="21" y2="6"></line>
                        <line x1="8" y1="12" x2="21" y2="12"></line>
                        <line x1="8" y1="18" x2="21" y2="18"></line>
                        <line x1="3" y1="6" x2="3.01" y2="6"></line>
                        <line x1="3" y1="12" x2="3.01" y2="12"></line>
                        <line x1="3" y1="18" x2="3.01" y2="18"></line>
                    </svg>
                </div>
                <h4 class="admin-feature-title">Requisitos</h4>
                <p class="admin-feature-text">Administra el catálogo de requisitos que pueden asignarse a los
                    trámites.</p>
            </a>

            {{-- Documentos Personales (Catálogo) --}}
            <a href="{{ route('indexDocumentosPersonales') }}" class="admin-feature-card">
                <div class="admin-feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z">
                        </path>
                    </svg>
                </div>
                <h4 class="admin-feature-title">Catálogo documentos</h4>
                <p class="admin-feature-text">Administra el catálogo de documentos personales solicitados a los
                    ciudadanos.</p>
            </a>

            {{-- Documentos de Predios (Catálogo) --}}
            <a href="{{ route('indexPredios') }}" class="admin-feature-card">
                <div class="admin-feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                </div>
                <h4 class="admin-feature-title">Catálogo predios</h4>
                <p class="admin-feature-text">Administra el catálogo de documentos requeridos para los predios.</p>
            </a>

            {{-- Usuarios --}}
            <a href="{{ route('indexUsuarios') }}" class="admin-feature-card">
                <div class="admin-feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <h4 class="admin-feature-title">Usuarios</h4>
                <p class="admin-feature-text">Gestiona los usuarios registrados en el sistema de administración.</p>
            </a>
        </div>

        {{-- Solicitudes pendientes recientes --}}
        <section class="admin-pending-section">
            <h3 class="admin-pending-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                Solicitudes pendientes recientes
            </h3>

            @if ($solicitudesRecientes->isNotEmpty())
                <div class="admin-pending-list">
                    @foreach ($solicitudesRecientes as $solicitud)
                        <a href="{{ route('solicitudes.index') }}" class="admin-pending-item">
                            <span class="admin-pending-dot admin-pending-dot--warning"></span>
                            <span class="admin-pending-text">
                                Solicitud #{{ $solicitud->id_solicitud }} —
                                <strong>{{ $solicitud->tramite?->nombre_tramite ?? 'Trámite no disponible' }}</strong>
                            </span>
                            <span class="admin-pending-meta">
                                {{ optional($solicitud->created_at)->diffForHumans() ?? '—' }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="admin-pending-empty">
                    <i class="fa-solid fa-check-circle" style="color: #1e5c50;"></i>
                    <span>No hay solicitudes pendientes. Todo está al día.</span>
                </div>
            @endif
        </section>

        {{-- Resumen de catálogos --}}
        <section class="admin-pending-section">
            <h3 class="admin-pending-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                Resumen del sistema
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
                <div class="admin-pending-item" style="cursor: default; text-decoration: none;">
                    <span class="admin-pending-dot admin-pending-dot--info"></span>
                    <span class="admin-pending-text">
                        <strong>{{ $totalDependencias }}</strong> dependencias registradas
                    </span>
                </div>
                <div class="admin-pending-item" style="cursor: default; text-decoration: none;">
                    <span class="admin-pending-dot admin-pending-dot--info"></span>
                    <span class="admin-pending-text">
                        <strong>{{ $totalTramites }}</strong> trámites disponibles
                    </span>
                </div>
                <div class="admin-pending-item" style="cursor: default; text-decoration: none;">
                    <span class="admin-pending-dot admin-pending-dot--info"></span>
                    <span class="admin-pending-text">
                        <strong>{{ $totalRequisitos }}</strong> requisitos registrados
                    </span>
                </div>
                <div class="admin-pending-item" style="cursor: default; text-decoration: none;">
                    <span class="admin-pending-dot admin-pending-dot--info"></span>
                    <span class="admin-pending-text">
                        <strong>{{ $totalUsuarios }}</strong> usuarios registrados
                    </span>
                </div>
            </div>
        </section>

        {{-- CTA --}}
        <section class="admin-cta-section">
            <h2 class="admin-cta-title">Gestiona el sistema</h2>
            <p>Selecciona una sección del menú lateral o usa las tarjetas de acceso rápido para comenzar.</p>
        </section>

    </div>

    @if (session('status'))
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __('¡Bienvenido!') }}',
                        text: '{{ session('status') }}',
                        confirmButtonColor: '#1E5C50',
                        confirmButtonText: 'Comenzar',
                        timer: 4000,
                        timerProgressBar: true
                    });
                });
            </script>
        @endpush
    @endif
@endsection
