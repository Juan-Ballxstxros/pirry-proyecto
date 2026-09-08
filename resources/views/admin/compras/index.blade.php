@extends('layouts.admin')

@section('title', 'Compras de Insumos')
@section('page_title', 'Compras de insumos')
@section('page_subtitle', 'Historial de abastecimiento')

@section('content')
<div class="topbar">
    <div></div>
    <a class="btn btn-crear" href="{{ route('admin.compras.create') }}">+ Registrar compra</a>
</div>

<div class="tabla">
    <table>
        <tr>
            <th>#</th>
            <th>Ingrediente</th>
            <th>Proveedor</th>
            <th>Cantidad</th>
            <th>Precio unit.</th>
            <th>Total</th>
            <th>Fecha</th>
        </tr>
        @forelse($compras as $c)
        <tr>
            <td>{{ $c->id }}</td>
            <td>{{ $c->ingrediente?->nombre }}</td>
            <td>{{ $c->proveedor?->nombre }}</td>
            <td>{{ $c->cantidad }} {{ $c->ingrediente?->unidad_medida }}</td>
            <td>${{ number_format($c->precio_unitario, 0, ',', '.') }}</td>
            <td>${{ number_format($c->total, 0, ',', '.') }}</td>
            <td>{{ $c->fecha?->format('d/m/Y H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="7" class="empty-state">Aún no hay compras registradas.</td></tr>
        @endforelse
    </table>
</div>
@endsection
