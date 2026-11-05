@extends($layout)

@section('title', 'Pedidos')
@section('page_title', 'Gestión de pedidos')
@section('page_subtitle', 'Administra y controla todos los pedidos de PIRRY')

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

    $pedidosPagina = $pedidos->getCollection();

    $totalPendientes = $pedidosPagina->where('estado', 'pendiente')->count();
    $totalPreparacion = $pedidosPagina->where('estado', 'en_preparacion')->count();
    $totalListos = $pedidosPagina->where('estado', 'listo')->count();
    $totalEntregados = $pedidosPagina->where('estado', 'entregado')->count();

    $rutaPedidos = request()->routeIs('admin.*')
        ? route('admin.pedidos.index')
        : route('empleado.pedidos.index');
@endphp

<div class="pirry-pedidos">

    {{-- ENCABEZADO --}}
    <div class="rp-encabezado">
        <div>
            <span class="rp-etiqueta">PANEL DE CONTROL</span>
            <h2>Pedidos de PIRRY</h2>
            <p>Gestiona los pedidos y mantén cada orden bajo control.</p>
        </div>

        <div class="rp-total-general">
            <span>Total de pedidos</span>
            <strong>{{ $pedidos->total() }}</strong>
            <small>Resultados encontrados</small>
        </div>
    </div>

    {{-- TARJETAS DE ESTADOS --}}
    <div class="rp-resumen">

        <div class="rp-card rp-pendientes">
            <div class="rp-card-icono">◷</div>
            <div>
                <span>Pendientes</span>
                <strong>{{ $totalPendientes }}</strong>
                <small>En esta página</small>
            </div>
        </div>

        <div class="rp-card rp-preparacion">
            <div class="rp-card-icono">♨</div>
            <div>
                <span>En preparación</span>
                <strong>{{ $totalPreparacion }}</strong>
                <small>En esta página</small>
            </div>
        </div>

        <div class="rp-card rp-listos">
            <div class="rp-card-icono">✓</div>
            <div>
                <span>Listos</span>
                <strong>{{ $totalListos }}</strong>
                <small>En esta página</small>
            </div>
        </div>

        <div class="rp-card rp-entregados">
            <div class="rp-card-icono">★</div>
            <div>
                <span>Entregados</span>
                <strong>{{ $totalEntregados }}</strong>
                <small>En esta página</small>
            </div>
        </div>

    </div>

    {{-- FILTROS --}}
    <div class="rp-filtros">
        <div class="rp-filtros-titulo">
            <h3>Buscar pedidos</h3>
            <span>Encuentra una orden rápidamente</span>
        </div>

        <form method="GET" action="{{ $rutaPedidos }}" class="rp-form-filtros">

            <div class="rp-campo-busqueda">
                <span>⌕</span>
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Código o nombre del cliente..."
                >
            </div>

            <select name="estado" class="rp-select-filtro">
                <option value="">Todos los estados</option>
                @foreach($estadosLabels as $valor => $texto)
                    <option value="{{ $valor }}" @selected(request('estado') === $valor)>
                        {{ $texto }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="rp-btn-buscar">
                Buscar
            </button>

            @if(request('q') || request('estado'))
                <a href="{{ $rutaPedidos }}" class="rp-btn-limpiar">
                    Limpiar
                </a>
            @endif

        </form>
    </div>

    {{-- TABLA --}}
    <div class="rp-tabla-contenedor">

        <div class="rp-tabla-encabezado">
            <div>
                <h3>Listado de pedidos</h3>
                <p>Consulta los detalles y actualiza los estados.</p>
            </div>
            <span class="rp-contador">{{ $pedidos->count() }} pedidos en esta página</span>
        </div>

        <div class="rp-tabla-scroll">
            <table class="rp-tabla">
                <thead>
                    <tr>
                        <th>Pedido</th>
                        <th>Cliente</th>
                        <th>Productos</th>
                        <th>Total</th>
                        <th>Pago</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($pedidos as $pedido)
                        <tr>
                            <td>
                                <strong class="rp-codigo">
                                    #{{ $pedido->codigo }}
                                </strong>
                            </td>

                            <td>
                                <span class="rp-cliente">
                                    {{ $pedido->nombreCliente() }}
                                </span>
                            </td>

                            <td>
                                <div class="rp-productos">
                                    @foreach($pedido->items as $item)
                                        <span>
                                            {{ $item->cantidad }}×
                                            {{ $item->producto?->nombre ?? 'Producto eliminado' }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>

                            <td>
                                <strong class="rp-precio">
                                    ${{ number_format($pedido->total, 0, ',', '.') }}
                                </strong>
                            </td>

                            <td>
                                {{ ucfirst($pedido->forma_pago ?? '—') }}
                            </td>

                            <td>
                                <span class="rp-estado rp-estado-{{ $pedido->estado }}">
                                    {{ $estadosLabels[$pedido->estado] ?? ucfirst(str_replace('_', ' ', $pedido->estado)) }}
                                </span>
                            </td>

                            <td class="rp-fecha">
                                {{ $pedido->fecha?->format('d/m/Y') }}
                                <small>{{ $pedido->fecha?->format('H:i') }}</small>
                            </td>

                            <td>
                                <div class="rp-acciones">

                                    <select
                                        class="rp-select-estado"
                                        aria-label="Cambiar estado de {{ $pedido->codigo }}"
                                        onchange="
                                            if(confirm('¿Deseas cambiar el estado de este pedido?')) {
                                                document.getElementById('form-estado-{{ $pedido->id }}').querySelector('[name=estado]').value = this.value;
                                                document.getElementById('form-estado-{{ $pedido->id }}').submit();
                                            } else {
                                                this.value = '{{ $pedido->estado }}';
                                            }
                                        "
                                    >
                                        @foreach($estadosLabels as $valor => $texto)
                                            <option value="{{ $valor }}" @selected($pedido->estado === $valor)>
                                                {{ $texto }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <button
                                        type="button"
                                        class="rp-btn-detalle"
                                        onclick="abrirDetalle({{ $pedido->id }})"
                                    >
                                        Ver detalle
                                    </button>

                                </div>

                                <form
                                    id="form-estado-{{ $pedido->id }}"
                                    action="{{ request()->routeIs('admin.*') ? route('admin.pedidos.estado', $pedido) : route('empleado.pedidos.estado', $pedido) }}"
                                    method="POST"
                                    style="display:none;"
                                >
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="estado" value="{{ $pedido->estado }}">
                                </form>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="rp-vacio">
                                    <span>📦</span>
                                    <h3>No hay pedidos registrados</h3>
                                    <p>Cuando recibas nuevos pedidos aparecerán aquí.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINACIÓN --}}
    <div class="rp-paginacion">
        {{ $pedidos->links() }}
    </div>

</div>

{{-- MODALES DE DETALLE --}}
@foreach($pedidos as $pedido)

<div class="rp-overlay" id="pd-detalle-{{ $pedido->id }}" hidden>
    <div class="rp-modal" role="dialog" aria-modal="true">

        <div class="rp-modal-header">
            <div>
                <span class="rp-etiqueta">DETALLE DEL PEDIDO</span>
                <h2>#{{ $pedido->codigo }}</h2>
            </div>

            <button
                type="button"
                class="rp-cerrar"
                onclick="cerrarDetalle({{ $pedido->id }})"
            >×</button>
        </div>

        <div class="rp-modal-contenido">

            <span class="rp-estado rp-estado-{{ $pedido->estado }}">
                {{ $estadosLabels[$pedido->estado] ?? $pedido->estado }}
            </span>

            <div class="rp-info-grid">
                <div>
                    <small>Cliente</small>
                    <strong>{{ $pedido->nombreCliente() }}</strong>
                </div>

                <div>
                    <small>Fecha del pedido</small>
                    <strong>{{ $pedido->fecha?->format('d/m/Y H:i') }}</strong>
                </div>

                <div>
                    <small>Forma de pago</small>
                    <strong>{{ ucfirst($pedido->forma_pago ?? '—') }}</strong>
                </div>

                <div>
                    <small>Total del pedido</small>
                    <strong class="rp-precio">
                        ${{ number_format($pedido->total, 0, ',', '.') }}
                    </strong>
                </div>
            </div>

            <h3 class="rp-subtitulo">Productos del pedido</h3>

            <div class="rp-productos-tabla">
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($pedido->items as $item)
                            <tr>
                                <td>{{ $item->producto?->nombre ?? 'Producto eliminado' }}</td>
                                <td>{{ $item->cantidad }}</td>
                                <td>${{ number_format($item->precio_unitario, 0, ',', '.') }}</td>
                                <td>${{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <h3 class="rp-subtitulo">Historial de estados</h3>

            <ul class="rp-historial">
                @forelse($pedido->historial as $h)
                    <li>
                        <div class="rp-historial-punto"></div>

                        <div>
                            <strong>
                                @if($h->estado_anterior && $h->estado_anterior !== $h->estado_nuevo)
                                    {{ $estadosLabels[$h->estado_anterior] ?? $h->estado_anterior }}
                                    →
                                @endif

                                {{ $estadosLabels[$h->estado_nuevo] ?? $h->estado_nuevo }}
                            </strong>

                            <small>{{ $h->fecha?->format('d/m/Y H:i') }}</small>
                            <small>Por: {{ $h->usuario?->nombre ?? 'Sistema' }}</small>
                        </div>
                    </li>
                @empty
                    <li class="rp-historial-vacio">
                        Este pedido no tiene registros de historial.
                    </li>
                @endforelse
            </ul>

        </div>
    </div>
</div>

@endforeach

<style>
.pirry-pedidos {
    color: #e8e8e8;
    width: 100%;
}

.pirry-pedidos * {
    box-sizing: border-box;
}

.rp-encabezado {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 25px;
}

.rp-etiqueta {
    color: #d6a83c;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 2px;
}

.rp-encabezado h2 {
    color: #fff;
    font-size: 27px;
    margin: 7px 0;
    font-weight: 800;
}

.rp-encabezado p,
.rp-tabla-encabezado p {
    color: #999;
    margin: 0;
    font-size: 13px;
}

.rp-total-general {
    background: linear-gradient(135deg, #242424, #151515);
    border: 1px solid #3a3324;
    border-radius: 15px;
    padding: 17px 23px;
    min-width: 170px;
    display: flex;
    flex-direction: column;
}

.rp-total-general span,
.rp-total-general small {
    color: #aaa;
    font-size: 12px;
}

.rp-total-general strong {
    color: #e7bd57;
    font-size: 27px;
    margin: 3px 0;
}

.rp-resumen {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 15px;
    margin-bottom: 25px;
}

.rp-card {
    background: #1d1d1d;
    border: 1px solid #303030;
    border-radius: 15px;
    padding: 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
}

.rp-card-icono {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    font-size: 22px;
    font-weight: bold;
    background: #302a1d;
    color: #e7bd57;
}

.rp-card span,
.rp-card small {
    display: block;
    color: #aaa;
    font-size: 12px;
}

.rp-card strong {
    display: block;
    color: #fff;
    font-size: 25px;
    margin: 3px 0;
}

.rp-filtros,
.rp-tabla-contenedor {
    background: #1c1c1c;
    border: 1px solid #303030;
    border-radius: 16px;
    margin-bottom: 22px;
    overflow: hidden;
}

.rp-filtros {
    padding: 20px;
}

.rp-filtros-titulo h3,
.rp-tabla-encabezado h3 {
    color: #fff;
    font-size: 16px;
    margin: 0 0 5px;
}

.rp-filtros-titulo span {
    color: #999;
    font-size: 12px;
}

.rp-form-filtros {
    display: flex;
    gap: 10px;
    margin-top: 17px;
    flex-wrap: wrap;
}

.rp-campo-busqueda {
    flex: 1;
    min-width: 200px;
    display: flex;
    align-items: center;
    gap: 10px;
    background: #121212;
    border: 1px solid #393939;
    border-radius: 9px;
    padding: 0 13px;
}

.rp-campo-busqueda span {
    color: #d6a83c;
    font-size: 23px;
}

.rp-campo-busqueda input {
    width: 100%;
    min-width: 0;
    background: transparent;
    border: none;
    outline: none;
    color: white;
    padding: 13px 0;
}

.rp-select-filtro,
.rp-select-estado {
    background: #121212;
    color: #eee;
    border: 1px solid #393939;
    border-radius: 9px;
    padding: 11px;
}

.rp-btn-buscar,
.rp-btn-limpiar {
    border: none;
    border-radius: 9px;
    padding: 11px 20px;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.rp-btn-buscar {
    background: #d6a83c;
    color: #171717;
}

.rp-btn-buscar:hover {
    background: #edc75f;
}

.rp-btn-limpiar {
    background: #303030;
    color: #ddd;
}

.rp-tabla-encabezado {
    padding: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
}

.rp-contador {
    background: #302a1d;
    color: #e7bd57;
    border-radius: 20px;
    padding: 7px 12px;
    font-size: 11px;
    font-weight: 700;
}

.rp-tabla-scroll {
    width: 100%;
    overflow-x: auto;
}

.rp-tabla {
    width: 100%;
    min-width: 1050px;
    border-collapse: collapse;
}

.rp-tabla th {
    background: #252525;
    color: #aaa;
    text-align: left;
    padding: 14px 13px;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .5px;
    white-space: nowrap;
}

.rp-tabla td {
    border-top: 1px solid #303030;
    padding: 15px 13px;
    color: #ddd;
    font-size: 12px;
    vertical-align: middle;
}

.rp-tabla tbody tr:hover {
    background: #242424;
}

.rp-codigo,
.rp-precio {
    color: #e7bd57;
}

.rp-cliente {
    color: #fff;
    font-weight: 700;
}

.rp-productos {
    display: flex;
    flex-direction: column;
    gap: 4px;
    color: #bbb;
    max-width: 180px;
}

.rp-fecha {
    white-space: nowrap;
}

.rp-fecha small {
    display: block;
    color: #999;
    margin-top: 3px;
}

.rp-estado {
    display: inline-block;
    border-radius: 20px;
    padding: 6px 10px;
    font-size: 10px;
    font-weight: 800;
    white-space: nowrap;
    background: #333;
    color: #ddd;
}

.rp-estado-pendiente {
    background: #493a1d;
    color: #f2c65d;
}

.rp-estado-confirmado {
    background: #1c3b50;
    color: #72c5ff;
}

.rp-estado-en_preparacion {
    background: #49301d;
    color: #ffad67;
}

.rp-estado-listo {
    background: #1d4533;
    color: #76dfa1;
}

.rp-estado-en_camino {
    background: #30356a;
    color: #a9afff;
}

.rp-estado-entregado {
    background: #174c3c;
    color: #75e6bd;
}

.rp-estado-cancelado {
    background: #4b2424;
    color: #ff8b8b;
}

.rp-acciones {
    display: flex;
    flex-direction: column;
    gap: 7px;
    min-width: 120px;
}

.rp-select-estado {
    width: 100%;
    font-size: 11px;
    padding: 8px;
}

.rp-btn-detalle {
    background: #302a1d;
    color: #e7bd57;
    border: 1px solid #5b4926;
    border-radius: 7px;
    padding: 8px;
    font-weight: 700;
    cursor: pointer;
}

.rp-btn-detalle:hover {
    background: #4a3b20;
}

.rp-vacio {
    text-align: center;
    padding: 45px 15px;
}

.rp-vacio span {
    font-size: 35px;
}

.rp-vacio h3 {
    color: #fff;
    margin: 12px 0 5px;
}

.rp-vacio p {
    color: #999;
}

.rp-paginacion {
    margin: 20px 0;
}

/* MODAL */

.rp-overlay[hidden] {
    display: none !important;
}

.rp-overlay {
    position: fixed;
    inset: 0;
    z-index: 99999;
    background: rgba(0, 0, 0, .82);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    overflow-y: auto;
}

.rp-modal {
    background: #1c1c1c;
    border: 1px solid #514326;
    border-radius: 17px;
    width: 100%;
    max-width: 780px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 70px rgba(0,0,0,.5);
}

.rp-modal-header {
    padding: 22px;
    border-bottom: 1px solid #333;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.rp-modal-header h2 {
    color: #fff;
    margin: 5px 0 0;
}

.rp-cerrar {
    background: #333;
    color: white;
    border: none;
    width: 35px;
    height: 35px;
    border-radius: 9px;
    font-size: 23px;
    cursor: pointer;
}

.rp-modal-contenido {
    padding: 22px;
}

.rp-info-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 13px;
    margin: 20px 0;
}

.rp-info-grid div {
    background: #252525;
    border: 1px solid #353535;
    border-radius: 10px;
    padding: 13px;
}

.rp-info-grid small,
.rp-info-grid strong {
    display: block;
}

.rp-info-grid small {
    color: #999;
    margin-bottom: 7px;
}

.rp-info-grid strong {
    color: #fff;
    font-size: 13px;
}

.rp-subtitulo {
    color: #e7bd57;
    font-size: 15px;
    margin: 25px 0 12px;
}

.rp-productos-tabla {
    overflow-x: auto;
}

.rp-productos-tabla table {
    width: 100%;
    border-collapse: collapse;
    min-width: 450px;
}

.rp-productos-tabla th {
    color: #aaa;
    background: #292929;
    text-align: left;
    font-size: 11px;
    padding: 12px;
}

.rp-productos-tabla td {
    color: #ddd;
    border-bottom: 1px solid #333;
    padding: 12px;
    font-size: 12px;
}

.rp-historial {
    list-style: none;
    padding: 0;
    margin: 0;
}

.rp-historial li {
    display: flex;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #333;
}

.rp-historial-punto {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #d6a83c;
    margin-top: 5px;
    flex-shrink: 0;
}

.rp-historial li strong,
.rp-historial li small {
    display: block;
}

.rp-historial li strong {
    color: #fff;
    font-size: 13px;
}

.rp-historial li small {
    color: #999;
    margin-top: 5px;
}

.rp-historial-vacio {
    color: #999;
}

@media (max-width: 900px) {
    .rp-resumen {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 600px) {
    .rp-encabezado {
        align-items: flex-start;
        flex-direction: column;
    }

    .rp-total-general {
        width: 100%;
    }

    .rp-resumen {
        gap: 9px;
    }

    .rp-card {
        padding: 12px;
        gap: 9px;
    }

    .rp-card-icono {
        width: 35px;
        height: 35px;
        font-size: 17px;
    }

    .rp-card strong {
        font-size: 21px;
    }

    .rp-form-filtros {
        flex-direction: column;
    }

    .rp-campo-busqueda,
    .rp-select-filtro,
    .rp-btn-buscar,
    .rp-btn-limpiar {
        width: 100%;
    }

    .rp-tabla-encabezado {
        align-items: flex-start;
        flex-direction: column;
    }

    .rp-info-grid {
        grid-template-columns: 1fr;
    }

    .rp-overlay {
        padding: 8px;
    }
}
</style>

<script>
function abrirDetalle(id) {
    const modal = document.getElementById('pd-detalle-' + id);

    if (modal) {
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
    }
}

function cerrarDetalle(id) {
    const modal = document.getElementById('pd-detalle-' + id);

    if (modal) {
        modal.hidden = true;
        document.body.style.overflow = '';
    }
}

document.addEventListener('click', function(event) {
    if (event.target.classList && event.target.classList.contains('rp-overlay')) {
        event.target.hidden = true;
        document.body.style.overflow = '';
    }
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        document.querySelectorAll('.rp-overlay:not([hidden])').forEach(function(modal) {
            modal.hidden = true;
        });

        document.body.style.overflow = '';
    }
});
</script>

@endsection