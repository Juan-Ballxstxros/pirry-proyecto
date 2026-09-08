@extends('layouts.empleado')

@section('title', 'Pedidos')
@section('page_title', 'Pedidos')
@section('page_subtitle', 'Procesa y actualiza el estado de los pedidos')

@section('content')
<div class="tabla">
    <table>
        <tr>
            <th>#</th>
            <th>Cliente</th>
            <th>Productos</th>
            <th>Total</th>
            <th>Pago</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th>Acción</th>
        </tr>
        @forelse($pedidos as $pedido)
        <tr>
            <td>#{{ $pedido->id }}</td>
            <td>{{ $pedido->usuario?->nombre ?? 'Invitado' }}</td>
            <td>
                @foreach($pedido->items as $item)
                    {{ $item->cantidad }}× {{ $item->producto?->nombre ?? 'Producto eliminado' }}@if(!$loop->last), @endif
                @endforeach
            </td>
            <td>${{ number_format($pedido->total, 0, ',', '.') }}</td>
            <td>{{ ucfirst($pedido->forma_pago ?? '—') }}</td>
            <td><span class="pill pill-info">{{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}</span></td>
            <td>{{ $pedido->fecha?->format('d/m/Y H:i') }}</td>
            <td>
                <form action="{{ route('empleado.pedidos.estado', $pedido) }}" method="POST" class="formulario form-inline" style="gap:6px;">
                    @csrf
                    @method('PATCH')
                    <select name="estado" onchange="this.form.submit()" style="min-width:130px;">
                        @foreach(['en_proceso','preparando','listo','entregado','cancelado'] as $estado)
                            <option value="{{ $estado }}" @selected($pedido->estado === $estado)>{{ ucfirst(str_replace('_',' ', $estado)) }}</option>
                        @endforeach
                    </select>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="empty-state">No hay pedidos registrados.</td></tr>
        @endforelse
    </table>
</div>

{{ $pedidos->links() }}
@endsection
