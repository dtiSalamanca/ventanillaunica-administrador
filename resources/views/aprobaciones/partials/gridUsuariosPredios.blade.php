@if ($usuarios->isEmpty())
    <div class="empty-state">
        <i class="fa-solid {{ $pendiente ? 'fa-circle-check' : 'fa-inbox' }}"></i>
        @if ($query)
            <p>No se encontraron usuarios que coincidan con «{{ $query }}».</p>
        @else
            <p>{{ $pendiente ? 'No hay predios pendientes de revisión.' : 'No hay usuarios con predios ya revisados.' }}</p>
        @endif
    </div>
@else
    <div class="aprobaciones-grid">
        @foreach ($usuarios as $usuario)
            @include('aprobaciones.partials.tarjetaUsuarioPredio', ['usuario' => $usuario, 'pendiente' => $pendiente, 'prefijo' => $prefijo])
        @endforeach
    </div>

    <div class="aprobaciones-pagination">
        {{ $usuarios->links('vendor.pagination.aprobaciones') }}
    </div>
@endif
