@extends('layouts.empleado')

@section('title', 'Historial de Ventas')
@section('page_title', 'Historial de ventas')

@section('content')
<div class="card">
    <form method="GET" class="formulario form-inline">
        <div>
            <label>Desde</label>
            <input type="date" name="desde" value="{{ request('desde') }}">
        </div>
        <div>
            <label>Hasta</label>
            <input type="date" name="hasta" value="{{ request('hasta') }}">
        </div>
        <button type="submit" class="btn guardar">Filtrar</button>
    </form>
</div>

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
        </tr>
        @empty
        <tr><td colspan="7" class="empty-state">
            @if(request('desde') || request('hasta'))
                No hay ventas registradas en ese rango de fechas.
            @else
                Aún no hay ventas registradas.
            @endif
        </td></tr>
        @endforelse
    </table>
</div>

{{ $pedidos->links() }}
@endsection
