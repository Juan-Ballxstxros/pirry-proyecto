@extends('layouts.empleado')

@section('title', 'Panel Empleado')
@section('page_title', 'Inicio')
@section('page_subtitle', 'Resumen operativo del turno')

@section('content')

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 9h8M8 13h5"/></svg>
        </div>
        <div class="kpi-body">
            <div class="kpi-label">Pedidos pendientes</div>
            <div class="kpi-value">{{ $pedidosPendientes }}</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon orange">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="kpi-body">
            <div class="kpi-label">Ventas hoy</div>
            <div class="kpi-value">{{ $ventasHoy }}</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7 12 3 4 7v10l8 4 8-4Z"/><path d="M4 7l8 4 8-4M12 11v10"/></svg>
        </div>
        <div class="kpi-body">
            <div class="kpi-label">Ingredientes con stock bajo</div>
            <div class="kpi-value">{{ $ingredientesBajos }}</div>
        </div>
    </div>
</div>

<div class="topbar">
    <div></div>
    <a class="btn btn-crear" href="{{ route('empleado.ventas.create') }}">+ Registrar venta</a>
</div>

<div class="tabla">
    <h3>Últimos pedidos</h3>
    <table>
        <tr>
            <th>#</th>
            <th>Cliente</th>
            <th>Total</th>
            <th>Estado</th>
            <th>Fecha</th>
        </tr>
        @forelse($ultimosPedidos as $pedido)
        <tr>
            <td>#{{ $pedido->id }}</td>
            <td>{{ $pedido->usuario?->nombre ?? 'Invitado' }}</td>
            <td>${{ number_format($pedido->total, 0, ',', '.') }}</td>
            <td><span class="pill pill-info">{{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}</span></td>
            <td>{{ $pedido->fecha?->format('d/m/Y H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="empty-state">Aún no hay pedidos registrados.</td></tr>
        @endforelse
    </table>
</div>
@endsection
