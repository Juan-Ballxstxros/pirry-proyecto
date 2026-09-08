<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Admin - El Pirry')</title>
<link rel="stylesheet" href="{{ asset('css/interfaz_ad.css') }}">
<link rel="stylesheet" href="{{ asset('css/toast.css') }}">
@stack('styles')
</head>
<body>

<div class="dashboard">
    <aside class="sidebar">
        <h2 class="logo">Admin</h2>
        <ul>
            <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">Inicio</a>
            </li>
            <li class="{{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
                <a href="{{ route('admin.usuarios.index') }}">Usuarios</a>
            </li>
            <li class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                <a href="{{ route('admin.roles.index') }}">Roles</a>
            </li>
            <li class="{{ request()->routeIs('admin.permisos.*') ? 'active' : '' }}">
                <a href="{{ route('admin.permisos.index') }}">Permisos</a>
            </li>
            <li>
                <a href="{{ route('home') }}">Ver Tienda</a>
            </li>
            <li class="logout">
                <form action="{{ route('logout') }}" method="POST" onsubmit="return confirmLogout(this)">
                    @csrf
                    <button type="submit" class="sidebar-logout">Cerrar sesión</button>
                </form>
            </li>
        </ul>
    </aside>

    <main class="main">
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

</body>
</html>
