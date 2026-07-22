@extends('layouts.enlace')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/enlace/enlace_home.css') }}">
@endsection

@section('content')
    <div class="enlace-home-container">

        {{-- Hero --}}
        <section class="enlace-hero-section">
            <div class="enlace-hero-header">
                <div class="enlace-hero-bg-pattern"></div>
                <h1 class="enlace-hero-title">Panel de Enlace</h1>
                <p class="enlace-hero-subtitle">Sistema de Administración de la Ventanilla Única — H. Ayuntamiento de
                    Salamanca, Guanajuato</p>
            </div>
            <div class="enlace-hero-body">
                <p class="enlace-hero-welcome">
                    Bienvenido, <strong>{{ auth()->user()?->name ?? 'Enlace' }}</strong>
                </p>
                <p class="enlace-hero-description">
                    Desde este panel puedes dar seguimiento a los trámites que han sido turnados para su atención.
                </p>
            </div>
        </section>

        {{-- Stats --}}
        <div class="enlace-stats-grid">
            <div class="enlace-stat-card">
                <div class="enlace-stat-icon enlace-stat-icon--primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                </div>
                <div class="enlace-stat-info">
                    <div class="enlace-stat-number">{{ $totalTurnados }}</div>
                    <p class="enlace-stat-label">Trámites turnados</p>
                    <a href="{{ route('enlace.tramitesTurnados') }}" class="enlace-stat-link">Ver todos &rarr;</a>
                </div>
            </div>

            <div class="enlace-stat-card">
                <div class="enlace-stat-icon enlace-stat-icon--warning">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <div class="enlace-stat-info">
                    <div class="enlace-stat-number">{{ $turnadosPendientes }}</div>
                    <p class="enlace-stat-label">Pendientes de atención</p>
                    <a href="{{ route('enlace.tramitesTurnados') }}" class="enlace-stat-link">Revisar &rarr;</a>
                </div>
            </div>

            <div class="enlace-stat-card">
                <div class="enlace-stat-icon enlace-stat-icon--success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 11 12 14 22 4"></polyline>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                    </svg>
                </div>
                <div class="enlace-stat-info">
                    <div class="enlace-stat-number">{{ $turnadosCompletados }}</div>
                    <p class="enlace-stat-label">Atendidos</p>
                    <a href="{{ route('enlace.tramitesTurnados') }}" class="enlace-stat-link">Ver &rarr;</a>
                </div>
            </div>
        </div>

        {{-- Accesos rápidos --}}
        <h3 class="enlace-section-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline>
                <path
                    d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z">
                </path>
            </svg>
            Accesos rápidos
        </h3>

        <div class="enlace-features-grid">
            <a href="{{ route('enlace.tramitesTurnados') }}" class="enlace-feature-card">
                <div class="enlace-feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                </div>
                <h4 class="enlace-feature-title">Trámites Turnados</h4>
                <p class="enlace-feature-text">Revisa y da seguimiento a los trámites que han sido turnados para su
                    atención.</p>
            </a>
        </div>

        {{-- Turnados recientes --}}
        <section class="enlace-pending-section">
            <h3 class="enlace-pending-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                Turnados recientes
            </h3>

            @if ($turnadosRecientes->isNotEmpty())
                <div class="enlace-pending-list">
                    @foreach ($turnadosRecientes as $turnado)
                        <a href="{{ route('enlace.tramitesTurnados') }}" class="enlace-pending-item">
                            <span
                                class="enlace-pending-dot @if ($turnado->estatus_turnado) enlace-pending-dot--success @else enlace-pending-dot--warning @endif"></span>
                            <span class="enlace-pending-text">
                                Solicitud #{{ $turnado->solicitud?->id_solicitud ?? '—' }} —
                                <strong>{{ $turnado->solicitud?->tramite?->nombre_tramite ?? 'Trámite no disponible' }}</strong>
                            </span>
                            <span class="enlace-pending-meta">
                                {{ optional($turnado->created_at)->diffForHumans() ?? '—' }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="enlace-pending-empty">
                    <i class="fa-solid fa-check-circle" style="color: #1e5c50;"></i>
                    <span>No hay trámites turnados. Todo está al día.</span>
                </div>
            @endif
        </section>

    </div>
@endsection
