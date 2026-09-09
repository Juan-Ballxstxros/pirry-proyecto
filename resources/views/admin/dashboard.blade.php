@extends('layouts.admin')

@section('title', 'Panel Administrador')
@section('page_title', 'Inicio')
@section('page_subtitle', 'Resumen general de la tienda')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
@endpush

@section('content')

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-icon orange">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="kpi-body">
            <div class="kpi-label">Ingresos totales</div>
            <div class="kpi-value">${{ number_format($ingresosTotales, 0, ',', '.') }}</div>
            <div class="kpi-sub">Hoy: ${{ number_format($ingresosHoy, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6h15l-1.5 9h-12z"/><path d="M6 6 5 3H2"/><circle cx="9" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/></svg>
        </div>
        <div class="kpi-body">
            <div class="kpi-label">Pedidos</div>
            <div class="kpi-value">{{ $totalPedidos }}</div>
            <div class="kpi-sub">Hoy: {{ $pedidosHoy }} pedido{{ $pedidosHoy === 1 ? '' : 's' }}</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="8" r="3.2"/><path d="M2.5 20c0-3.6 2.9-6.2 6.5-6.2s6.5 2.6 6.5 6.2"/><path d="M16.5 6.7a3 3 0 0 1 0 5.8"/><path d="M19.5 20c0-2.9-1.9-5.2-4.5-6"/></svg>
        </div>
        <div class="kpi-body">
            <div class="kpi-label">Usuarios</div>
            <div class="kpi-value">{{ $totalUsuarios }}</div>
            <div class="kpi-sub">{{ $porcentaje }}% activos ({{ $totalActivos }})</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7 12 3 4 7v10l8 4 8-4Z"/><path d="M4 7l8 4 8-4M12 11v10"/></svg>
        </div>
        <div class="kpi-body">
            <div class="kpi-label">Productos</div>
            <div class="kpi-value">{{ $totalProductos }}</div>
            <div class="kpi-sub">{{ $productosActivos }} activos</div>
        </div>
    </div>
</div>

<div class="charts-grid">
    <div class="chart-card">
        <div class="chart-card-head">
            <h3>Ventas (últimos 14 días)</h3>
            <a href="{{ route('admin.estadisticas.index') }}" class="link-cta">Ver estadísticas completas →</a>
        </div>
        <div class="chart-wrap">
            <canvas id="chartVentas"></canvas>
        </div>
    </div>

    <div class="chart-card">
        <div class="chart-card-head">
            <h3>Pedidos por estado</h3>
        </div>
        <div class="chart-wrap">
            <canvas id="chartEstados"></canvas>
        </div>
    </div>
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

<div class="tabla">
    <h3>Últimos usuarios registrados</h3>
    <table>
        <tr>
            <th>Usuario</th>
            <th>Estado</th>
        </tr>
        @forelse($ultimos as $u)
        <tr>
            <td>{{ $u->nombre }}</td>
            <td><span class="pill {{ strtolower($u->estado) === 'activo' ? 'pill-activo' : 'pill-inactivo' }}">{{ ucfirst($u->estado) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="2" class="empty-state">No hay datos</td></tr>
        @endforelse
    </table>
</div>

@endsection

@push('scripts')
<script>
(function () {
    fetch('{{ route('admin.api.estadisticas') }}?dias=14')
        .then(async (r) => {
            if (!r.ok) {
                const texto = await r.text();
                throw new Error('HTTP ' + r.status + ': ' + texto.slice(0, 500));
            }
            return r.json();
        })
        .then(data => {
            const labels = data.ventas_por_dia.map(d => {
                const [y, m, day] = d.fecha.split('-');
                return `${day}/${m}`;
            });

            new Chart(document.getElementById('chartVentas'), {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Ventas ($)',
                        data: data.ventas_por_dia.map(d => d.total),
                        borderColor: '#ff8c42',
                        backgroundColor: 'rgba(255,140,66,0.12)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });

            const estadosEl = document.getElementById('chartEstados');
            if (data.pedidos_por_estado.length === 0) {
                estadosEl.closest('.chart-wrap').innerHTML = '<div class="chart-empty">Aún no hay pedidos en este periodo.</div>';
                return;
            }

            new Chart(estadosEl, {
                type: 'doughnut',
                data: {
                    labels: data.pedidos_por_estado.map(d => d.estado),
                    datasets: [{
                        data: data.pedidos_por_estado.map(d => d.total),
                        backgroundColor: ['#ff8c42', '#2563eb', '#16a34a', '#d97706', '#7c3aed', '#dc2626'],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        })
        .catch((err) => {
            console.error(err);
            document.getElementById('chartVentas').closest('.chart-wrap').innerHTML =
                '<div class="chart-empty" style="text-align:left; white-space:pre-wrap; font-family:monospace; font-size:11px;">' +
                'ERROR AL CARGAR ESTADÍSTICAS:\n' + err.message + '</div>';
        });
})();
</script>
@endpush