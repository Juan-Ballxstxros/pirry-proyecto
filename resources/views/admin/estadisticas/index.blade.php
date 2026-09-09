@extends('layouts.admin')

@section('title', 'Estadísticas')
@section('page_title', 'Estadísticas')
@section('page_subtitle', 'Ventas, pedidos, productos y usuarios')

@push('scripts')
<!-- Librería Chart.js desde jsdelivr -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
@endpush

@section('content')

<div class="kpi-grid" id="kpiGrid">
    <div class="kpi-card">
        <div class="kpi-icon orange">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="kpi-body">
            <div class="kpi-label">Ingresos en el periodo</div>
            <div class="kpi-value" id="kpiIngresos">—</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6h15l-1.5 9h-12z"/><path d="M6 6 5 3H2"/><circle cx="9" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/></svg>
        </div>
        <div class="kpi-body">
            <div class="kpi-label">Pedidos en el periodo</div>
            <div class="kpi-value" id="kpiPedidos">—</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M7 15l4-5 3 3 5-7"/></svg>
        </div>
        <div class="kpi-body">
            <div class="kpi-label">Ticket promedio</div>
            <div class="kpi-value" id="kpiTicket">—</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="8" r="3.2"/><path d="M2.5 20c0-3.6 2.9-6.2 6.5-6.2s6.5 2.6 6.5 6.2"/><path d="M16.5 6.7a3 3 0 0 1 0 5.8"/><path d="M19.5 20c0-2.9-1.9-5.2-4.5-6"/></svg>
        </div>
        <div class="kpi-body">
            <div class="kpi-label">Usuarios activos</div>
            <div class="kpi-value" id="kpiUsuarios">—</div>
        </div>
    </div>
</div>

<div class="chart-card" style="margin-bottom:18px;">
    <div class="chart-card-head">
        <h3>Ventas por día</h3>
        <div class="period-select" id="periodSelect">
            <button type="button" data-dias="7">7 días</button>
            <button type="button" data-dias="30" class="active">30 días</button>
            <button type="button" data-dias="90">90 días</button>
        </div>
    </div>
    <div class="chart-wrap">
        <canvas id="chartVentas"></canvas>
    </div>
</div>

<div class="charts-grid">
    <div class="chart-card">
        <div class="chart-card-head"><h3>Productos más vendidos</h3></div>
        <div class="chart-wrap chart-sm">
            <canvas id="chartProductos"></canvas>
        </div>
    </div>
    <div class="chart-card">
        <div class="chart-card-head"><h3>Pedidos por estado</h3></div>
        <div class="chart-wrap chart-sm">
            <canvas id="chartEstados"></canvas>
        </div>
    </div>
</div>

<div class="chart-card" style="margin-bottom:18px;">
    <div class="chart-card-head">
        <h3>Ventas por horario (horarios pico)</h3>
        <span class="text-muted" id="horariosDestacados" style="font-size:13px;"></span>
    </div>
    <div class="chart-wrap">
        <canvas id="chartHorarios"></canvas>
    </div>
</div>

<div class="chart-card">
    <div class="chart-card-head"><h3>Usuarios por rol</h3></div>
    <div class="chart-wrap chart-sm">
        <canvas id="chartRoles"></canvas>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    const apiUrl = '{{ route('admin.api.estadisticas') }}';
    let charts = {};

    function destroyCharts() {
        Object.values(charts).forEach(c => c?.destroy());
        charts = {};
    }

    function money(n) {
        return '$' + Number(n).toLocaleString('es-CO', { maximumFractionDigits: 0 });
    }

    function cargar(dias) {
        fetch(apiUrl + '?dias=' + dias)
            .then(r => r.json())
            .then(data => render(data));
    }

    function render(data) {
        destroyCharts();

        document.getElementById('kpiIngresos').textContent = money(data.resumen.ingresos_totales);
        document.getElementById('kpiPedidos').textContent = data.resumen.pedidos_totales;
        document.getElementById('kpiTicket').textContent = money(data.resumen.ticket_promedio);
        document.getElementById('kpiUsuarios').textContent = data.resumen.usuarios_activos;

        const labels = data.ventas_por_dia.map(d => {
            const [y, m, day] = d.fecha.split('-');
            return `${day}/${m}`;
        });

        charts.ventas = new Chart(document.getElementById('chartVentas'), {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Ventas ($)',
                    data: data.ventas_por_dia.map(d => d.total),
                    borderColor: '#ff8c42',
                    backgroundColor: 'rgba(255,140,66,0.12)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: data.ventas_por_dia.length > 40 ? 0 : 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        const prodEl = document.getElementById('chartProductos');
        if (data.productos_top.length === 0) {
            prodEl.closest('.chart-wrap').innerHTML = '<div class="chart-empty">Aún no hay ventas de productos en este periodo.</div>';
        } else {
            charts.productos = new Chart(prodEl, {
                type: 'bar',
                data: {
                    labels: data.productos_top.map(p => p.nombre),
                    datasets: [{
                        label: 'Unidades vendidas',
                        data: data.productos_top.map(p => p.cantidad),
                        backgroundColor: '#ff8c42',
                        borderRadius: 6,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { x: { beginAtZero: true } }
                }
            });
        }

        const estadosEl = document.getElementById('chartEstados');
        if (data.pedidos_por_estado.length === 0) {
            estadosEl.closest('.chart-wrap').innerHTML = '<div class="chart-empty">Aún no hay pedidos en este periodo.</div>';
        } else {
            charts.estados = new Chart(estadosEl, {
                type: 'doughnut',
                data: {
                    labels: data.pedidos_por_estado.map(d => d.estado),
                    datasets: [{
                        data: data.pedidos_por_estado.map(d => d.total),
                        backgroundColor: ['#ff8c42', '#2563eb', '#16a34a', '#d97706', '#7c3aed', '#dc2626'],
                        borderWidth: 0,
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
            });
        }

        document.getElementById('horariosDestacados').textContent =
            data.horarios_pico.horarios_destacados.length
                ? 'Horas pico: ' + data.horarios_pico.horarios_destacados.join(', ')
                : '';

        charts.horarios = new Chart(document.getElementById('chartHorarios'), {
            type: 'bar',
            data: {
                labels: data.horarios_pico.serie.map(h => h.etiqueta),
                datasets: [{
                    label: 'Ventas ($)',
                    data: data.horarios_pico.serie.map(h => h.total),
                    backgroundColor: data.horarios_pico.serie.map(h =>
                        data.horarios_pico.horarios_destacados.includes(h.etiqueta) ? '#ff8c42' : '#ffd8b8'
                    ),
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        const rolesEl = document.getElementById('chartRoles');
        if (data.usuarios_por_rol.length === 0) {
            rolesEl.closest('.chart-wrap').innerHTML = '<div class="chart-empty">No hay usuarios registrados todavía.</div>';
        } else {
            charts.roles = new Chart(rolesEl, {
                type: 'bar',
                data: {
                    labels: data.usuarios_por_rol.map(r => r.rol),
                    datasets: [{
                        label: 'Usuarios',
                        data: data.usuarios_por_rol.map(r => r.total),
                        backgroundColor: '#2563eb',
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        }
    }

    document.getElementById('periodSelect').addEventListener('click', function (e) {
        const btn = e.target.closest('button[data-dias]');
        if (!btn) return;
        this.querySelectorAll('button').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        cargar(btn.dataset.dias);
    });

    cargar(30);
})();
</script>
@endpush