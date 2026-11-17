<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Panel Empleado - El Pirry')</title>
<link rel="stylesheet" href="{{ asset('css/admin-panel.css') }}">
<link rel="stylesheet" href="{{ asset('css/toast.css') }}">
@stack('styles')
</head>
<body>

<div class="dashboard">
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <aside class="sidebar" id="sidebar">
        <div class="logo">
            <span class="logo-badge">P</span>
            <span class="logo-text">El Pirry<small>Panel empleados</small></span>
        </div>

        <ul class="sidebar-nav">
            <li class="{{ request()->routeIs('empleado.dashboard') ? 'active' : '' }}">
                <a href="{{ route('empleado.dashboard') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M9 21v-6h6v6"/></svg>
                    Inicio
                </a>
            </li>
            <li class="{{ request()->routeIs('empleado.pedidos.*') ? 'active' : '' }}">
                <a href="{{ route('empleado.pedidos.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 9h8M8 13h5"/></svg>
                    Pedidos
                </a>
            </li>
            <li class="{{ request()->routeIs('empleado.ventas.*') ? 'active' : '' }}">
                <a href="{{ route('empleado.ventas.create') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    Registrar venta
                </a>
            </li>
            <li class="{{ request()->routeIs('empleado.historial') ? 'active' : '' }}">
                <a href="{{ route('empleado.historial') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M7 15l4-5 3 3 5-7"/></svg>
                    Historial de ventas
                </a>
            </li>
            <li class="{{ request()->routeIs('empleado.inventario') ? 'active' : '' }}">
                <a href="{{ route('empleado.inventario') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7 12 3 4 7v10l8 4 8-4Z"/><path d="M4 7l8 4 8-4M12 11v10"/></svg>
                    Inventario
                </a>
            </li>

            <li class="sidebar-section-label">Tienda</li>
            <li>
                <a href="{{ route('home') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9.5 12 4l9 5.5"/><path d="M4.5 8.5V20h15V8.5"/></svg>
                    Ver Tienda
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST" onsubmit="return confirmLogout(this)">
                @csrf
                <button type="submit" class="sidebar-logout">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    <main class="main">
        <div class="topbar">
            <div style="display:flex; align-items:center; gap:12px;">
                <button class="sidebar-toggle" id="sidebarToggle" aria-label="Abrir menú">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="topbar-title">
                    <h1>@yield('page_title', 'Panel')</h1>
                    @hasSection('page_subtitle')
                        <small>@yield('page_subtitle')</small>
                    @endif
                </div>
            </div>
            <div class="topbar-right">
                <div class="user">
                    <span class="user-avatar">{{ strtoupper(substr(Auth::user()->nombre ?? 'E', 0, 1)) }}</span>
                    {{ Auth::user()->nombre ?? 'Empleado' }}
                </div>
            </div>
        </div>

        @yield('content')
    </main>
</div>

<script>
window.flashMessages = window.flashMessages || [];
@if(session('success'))
  window.flashMessages.push({ type: 'success', message: @json(session('success')) });
@endif
@if(session('error'))
  window.flashMessages.push({ type: 'error', message: @json(session('error')) });
@endif
</script>
<script src="{{ asset('js/toast.js') }}"></script>

<script>
(function () {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const toggle = document.getElementById('sidebarToggle');
    function closeSidebar() { sidebar.classList.remove('open'); backdrop.classList.remove('open'); }
    toggle?.addEventListener('click', function () {
        sidebar.classList.toggle('open');
        backdrop.classList.toggle('open');
    });
    backdrop?.addEventListener('click', closeSidebar);
})();
</script>

@stack('scripts')
</body>
</html>