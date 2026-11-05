@extends($layout)

@section('title', 'Pedidos')
@section('page_title', 'Pedidos')
@section('page_subtitle', 'Procesa, busca y actualiza el estado de los pedidos')

@section('content')
@php
    $estadosLabels = [
        'pendiente' => 'Pendiente',
        'confirmado' => 'Confirmado',
        'en_preparacion' => 'En preparación',
        'listo' => 'Listo',
        'en_camino' => 'En camino',
        'entregado' => 'Entregado',
        'cancelado' => 'Cancelado',
    ];
@endphp

{{-- Búsqueda por código / nombre del cliente + filtro por estado --}}
<form method="GET" action="{{ request()->routeIs('admin.*') ? route('admin.pedidos.index') : route('empleado.pedidos.index') }}" class="pd-filtros">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por código o nombre del cliente…" class="pd-input-busqueda">
    <select name="estado" class="pd-input-estado">
        <option value="">Todos los estados</option>
        @foreach($estadosLabels as $valor => $texto)
            <option value="{{ $valor }}" @selected(request('estado') === $valor)>{{ $texto }}</option>
        @endforeach
    </select>
    <button type="submit" class="pd-btn-filtrar">Buscar</button>
    @if(request('q') || request('estado'))
        <a href="{{ request()->routeIs('admin.*') ? route('admin.pedidos.index') : route('empleado.pedidos.index') }}" class="pd-btn-limpiar">Limpiar</a>
    @endif
</form>

<div class="tabla">
    <table>
        <tr>
            <th>Código</th>
            <th>A nombre de</th>
            <th>Productos</th>
            <th>Total</th>
            <th>Pago</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th>Acción</th>
        </tr>
        @forelse($pedidos as $pedido)
        <tr>
            <td><strong>{{ $pedido->codigo }}</strong></td>
            <td>{{ $pedido->nombreCliente() }}</td>
            <td>
                @foreach($pedido->items as $item)
                    {{ $item->cantidad }}× {{ $item->producto?->nombre ?? 'Producto eliminado' }}@if(!$loop->last), @endif
                @endforeach
            </td>
            <td>${{ number_format($pedido->total, 0, ',', '.') }}</td>
            <td>{{ ucfirst($pedido->forma_pago ?? '—') }}</td>
            <td><span class="pill pill-estado pill-estado-{{ $pedido->estado }}">{{ $estadosLabels[$pedido->estado] ?? ucfirst(str_replace('_', ' ', $pedido->estado)) }}</span></td>
            <td>{{ $pedido->fecha?->format('d/m/Y H:i') }}</td>
            <td>
                <div class="pd-acciones">
                    <select onchange="document.getElementById('form-estado-{{ $pedido->id }}').estado.value=this.value; document.getElementById('form-estado-{{ $pedido->id }}').submit();"
                            aria-label="Cambiar estado de {{ $pedido->codigo }}">
                        @foreach($estadosLabels as $valor => $texto)
                            <option value="{{ $valor }}" @selected($pedido->estado === $valor)>{{ $texto }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="pd-btn-detalle" onclick="abrirDetalle({{ $pedido->id }})">Detalle</button>
                </div>
                {{-- Formulario oculto: el select visible solo lanza el PATCH --}}
                <form id="form-estado-{{ $pedido->id }}" action="{{ request()->routeIs('admin.*') ? route('admin.pedidos.estado', $pedido) : route('empleado.pedidos.estado', $pedido) }}" method="POST" class="formulario form-inline" style="display:none;">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="estado" value="{{ $pedido->estado }}">
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="empty-state">No hay pedidos registrados.</td></tr>
        @endforelse
    </table>
</div>

{{ $pedidos->links() }}

{{-- Modal de detalle: productos + historial de cambios de estado --}}
@foreach($pedidos as $pedido)
<div class="pd-overlay" id="pd-detalle-{{ $pedido->id }}" hidden>
    <div class="pd-modal" role="dialog" aria-modal="true" aria-label="Detalle del pedido {{ $pedido->codigo }}">
        <button type="button" class="pd-cerrar" onclick="cerrarDetalle({{ $pedido->id }})" aria-label="Cerrar">✕</button>
        <h3 class="pd-modal-titulo">{{ $pedido->codigo }} <span class="pill pill-estado pill-estado-{{ $pedido->estado }}">{{ $estadosLabels[$pedido->estado] ?? $pedido->estado }}</span></h3>
        <div class="pd-modal-info">
            <p><strong>Cliente:</strong> {{ $pedido->nombreCliente() }}</p>
            <p><strong>Fecha del pedido:</strong> {{ $pedido->fecha?->format('d/m/Y H:i') }}</p>
            <p><strong>Forma de pago:</strong> {{ ucfirst($pedido->forma_pago ?? '—') }}</p>
            <p><strong>Total:</strong> ${{ number_format($pedido->total, 0, ',', '.') }}</p>
        </div>
        <h4>Productos</h4>
        <table class="tabla pd-tabla-productos">
            <tr><th>Producto</th><th>Cant.</th><th>Precio unitario</th><th>Subtotal</th></tr>
            @foreach($pedido->items as $item)
            <tr>
                <td>{{ $item->producto?->nombre ?? 'Producto eliminado' }}</td>
                <td>{{ $item->cantidad }}</td>
                <td>${{ number_format($item->precio_unitario, 0, ',', '.') }}</td>
                <td>${{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </table>
        <h4>Historial de estados</h4>
        <ul class="pd-historial">
            @forelse($pedido->historial as $h)
            <li>
                <span class="pd-hist-fecha">{{ $h->fecha?->format('d/m/Y H:i') }}</span>
                <span class="pd-hist-cambio">
                    @if($h->estado_anterior && $h->estado_anterior !== $h->estado_nuevo)
                        {{ $estadosLabels[$h->estado_anterior] ?? $h->estado_anterior }} →
                        <strong>{{ $estadosLabels[$h->estado_nuevo] ?? $h->estado_nuevo }}</strong>
                    @else
                        <strong>{{ $estadosLabels[$h->estado_nuevo] ?? $h->estado_nuevo }}</strong>
                    @endif
                </span>
                <span class="pd-hist-por">{{ $h->usuario?->nombre ?? 'Sistema' }}</span>
            </li>
            @empty
            <li class="pd-hist-vacio">Este pedido no tiene registros de historial (creado antes del sistema de seguimiento).</li>
            @endforelse
        </ul>
    </div>
</div>
@endforeach

<script>
function abrirDetalle(id) {
    const el = document.getElementById('pd-detalle-' + id);
    if (el) el.hidden = false;
}
function cerrarDetalle(id) {
    const el = document.getElementById('pd-detalle-' + id);
    if (el) el.hidden = true;
}
// Cerrar al hacer clic en el fondo
document.addEventListener('click', function (e) {
    if (e.target.classList && e.target.classList.contains('pd-overlay')) {
        e.target.hidden = true;
    }
});
</script>
@endsection
