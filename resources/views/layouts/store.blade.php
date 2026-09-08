<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'El Pirry - Comidas Rápidas')</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/toast.css') }}">
@stack('styles')
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background: linear-gradient(90deg,#ff8c42,#e6702a);">
  <div class="container">
    <a class="navbar-brand fw-bold fs-3" href="{{ route('home') }}">El Pirry</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-label="Menú">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#Productos">Menú</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#Nosotros">Nosotros</a></li>
        <li class="nav-item">
          <a href="#" onclick="verCarrito(); return false;" class="nav-link cart-nav-btn" title="Ver carrito">🛒 <span id="navCartCount">0</span></a>
        </li>
        <li class="nav-item d-flex align-items-center gap-2 mt-2 mt-lg-0">
          @auth
            <span class="text-white-50 small user-greet">Hola, {{ Auth::user()->nombre }}</span>
            @if(Auth::user()->isAdmin())
              <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm fw-semibold">Panel Admin</a>
            @elseif(Auth::user()->isEmpleado())
              <a href="{{ route('empleado.dashboard') }}" class="btn btn-light btn-sm fw-semibold">Panel Empleado</a>
            @endif
            <form action="{{ route('logout') }}" method="POST" class="d-inline" onsubmit="return confirmLogout(this)">
              @csrf
              <button type="submit" class="btn btn-light btn-sm fw-semibold">Cerrar sesión</button>
            </form>
          @else
            <a href="{{ route('login') }}" class="btn btn-light btn-sm fw-semibold">Iniciar sesión</a>
          @endauth
        </li>
      </ul>
    </div>
  </div>
</nav>

@yield('content')

<footer>
  <div class="footer-content">
    <div class="footer-box">
      <h3>El Pirry</h3>
      <p>Las mejores salchipapas y hamburguesas de Guayabal 🍔🔥</p>
    </div>
    <div class="footer-box">
      <h3>Enlaces</h3>
      <ul>
        <li><a href="{{ route('home') }}">Inicio</a></li>
        <li><a href="{{ route('home') }}#Productos">Menú</a></li>
        <li><a href="{{ route('home') }}#Nosotros">Nosotros</a></li>
      </ul>
    </div>
    <div class="footer-box">
      <h3>Contacto</h3>
      <ul>
        <li>📍 Guayabal</li>
        <li>📞 300 123 4567</li>
        <li>📧 elpirry@gmail.com</li>
      </ul>
    </div>
    <div class="footer-box">
      <h3>Síguenos</h3>
      <div class="social">
        <a href="https://facebook.com" target="_blank" rel="noopener">FB</a>
        <a href="https://instagram.com" target="_blank" rel="noopener">IG</a>
        <a href="https://wa.me/573001234567" target="_blank" rel="noopener">WA</a>
      </div>
    </div>
  </div>
  <p class="footer-copy">© {{ date('Y') }} El Pirry - Todos los derechos reservados</p>
</footer>

<div id="carrito-badge" onclick="verCarrito()">
  🛒 <span id="countCarrito">0</span>
</div>

<script>
// Expuestos en window para que tanto el JS clásico como los módulos Vite
// (Vue) accedan a la configuración generada por Blade. El JS antiguo sigue
// funcionando igual: las referencias "routes", "csrfToken", etc. resuelven
// contra el objeto global.
window.isAdmin = {{ ($esAdmin ?? false) ? 'true' : 'false' }};
window.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
window.routes = {
  productos: '{{ url('/productos') }}',
  carritoAdd: '{{ route('carrito.add') }}',
  carritoShow: '{{ route('carrito.show') }}',
  carritoUpdate: '{{ route('carrito.update') }}',
  carritoRemove: '{{ route('carrito.remove') }}',
  carritoConfirm: '{{ route('carrito.confirm') }}',
  login: '{{ route('login') }}',
  register: '{{ route('register') }}',
};

// Configuración de WhatsApp del negocio (ver .env → WHATSAPP_NUMERO_NEGOCIO).
// Mientras el número esté vacío, el botón de WhatsApp solo muestra un aviso.
window.whatsappConfig = {
  numero: '{{ config('whatsapp.numero_negocio') }}',
};

// Puente del carrito: app.js (Vue) define la función real al montar el
// modal; este fallback evita errores si se hace clic antes de que cargue.
window.verCarrito = window.verCarrito || function () {};

// Mensajes flash de Laravel -> toasts bonitos
window.flashMessages = window.flashMessages || [];
@if(session('success'))
  window.flashMessages.push({ type: 'success', message: @json(session('success')) });
@endif
@if(session('error'))
  window.flashMessages.push({ type: 'error', message: @json(session('error')) });
@endif
</script>
{{-- index.js (carrito/menú/CRUD clásico) ya NO se carga en esta página:
    sus funciones las cubren ahora los componentes Vue. El archivo se
    conserva en public/js/index.js como referencia. toast.js sigue activo. --}}
<script src="{{ asset('js/toast.js') }}"></script>
{{-- Vite + Vue: compila resources/js/app.js. En producción carga el build;
    en desarrollo (npm run dev) conecta con el servidor HMR. --}}
@vite(['resources/js/app.js'])
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
