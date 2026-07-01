@if ($usuarios->isEmpty())
    <div class="empty-state">
        <i class="fa-solid {{ $pendiente ? 'fa-circle-check' : 'fa-inbox' }}"></i>
        @if ($query)
            <p>No se encontraron usuarios que coincidan con «{{ $query }}».</p>
        @else
            <p>{{ $pendiente ? 'No hay documentos personales pendientes de revisión.' : 'No hay usuarios con documentos ya revisados.' }}</p>
        @endif
    </div>
@else
    <div class="aprobaciones-grid">
        @foreach ($usuarios as $usuario)
            @include('aprobaciones.partials.tarjetaUsuario', ['usuario' => $usuario, 'pendiente' => $pendiente, 'prefijo' => $prefijo])
        @endforeach
    </div>

    <div class="aprobaciones-pagination">
        {{ $usuarios->links('pagination::bootstrap-5') }}
    </div>
@endif
