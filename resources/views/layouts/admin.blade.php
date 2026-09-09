<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Admin - El Pirry')</title>
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
            <span class="logo-text">El Pirry<small>Panel administrador</small></span>
        </div>

        <ul class="sidebar-nav">
            <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M9 21v-6h6v6"/></svg>
                    Inicio
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.estadisticas.*') ? 'active' : '' }}">
                <a href="{{ route('admin.estadisticas.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M7 15l4-5 3 3 5-7"/></svg>
                    Estadísticas
                </a>
            </li>

            <li class="sidebar-section-label">Operación</li>

            <li class="{{ request()->routeIs('admin.pedidos.*') ? 'active' : '' }}">
                <a href="{{ route('admin.pedidos.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 9h8M8 13h5"/></svg>
                    Pedidos
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.ventas.*') ? 'active' : '' }}">
                <a href="{{ route('admin.ventas.create') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    Registrar venta
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.historial') ? 'active' : '' }}">
                <a href="{{ route('admin.historial') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 2"/><circle cx="12" cy="12" r="9"/></svg>
                    Historial de ventas
                </a>
            </li>

            <li class="sidebar-section-label">Gestión</li>

            <li class="{{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
                <a href="{{ route('admin.usuarios.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="8" r="3.2"/><path d="M2.5 20c0-3.6 2.9-6.2 6.5-6.2s6.5 2.6 6.5 6.2"/><path d="M16.5 6.7a3 3 0 0 1 0 5.8"/><path d="M19.5 20c0-2.9-1.9-5.2-4.5-6"/></svg>
                    Usuarios
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                <a href="{{ route('admin.roles.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3 4 6.5V11c0 5 3.4 8.7 8 10 4.6-1.3 8-5 8-10V6.5L12 3Z"/></svg>
                    Roles
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.permisos.*') ? 'active' : '' }}">
                <a href="{{ route('admin.permisos.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="10.5" width="16" height="10" rx="2"/><path d="M8 10.5V7a4 4 0 0 1 8 0v3.5"/></svg>
                    Permisos
                </a>
            </li>

            <li class="sidebar-section-label">Inventario</li>

            <li class="{{ request()->routeIs('admin.proveedores.*') ? 'active' : '' }}">
                <a href="{{ route('admin.proveedores.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9.5 12 4l9 5.5"/><path d="M4.5 8.5V20h15V8.5"/><path d="M9 20v-6h6v6"/></svg>
                    Proveedores
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.ingredientes.*') ? 'active' : '' }}">
                <a href="{{ route('admin.ingredientes.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v6l4 4-2 10H10L8 12l4-4"/></svg>
                    Ingredientes
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.compras.*') ? 'active' : '' }}">
                <a href="{{ route('admin.compras.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6h15l-1.5 9h-12z"/><path d="M6 6 5 3H2"/><circle cx="9" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/></svg>
                    Compras
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
                    <span class="user-avatar">{{ strtoupper(substr(Auth::user()->nombre ?? 'A', 0, 1)) }}</span>
                    {{ Auth::user()->nombre ?? 'Administrador' }}
                </div>
            </div>
        </div>

        @yield('content')
    </main>
</div>

<script>
// Mensajes flash de Laravel -> toasts bonitos
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

    function closeSidebar() {
        sidebar.classList.remove('open');
        backdrop.classList.remove('open');
    }

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