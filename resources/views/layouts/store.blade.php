<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'El Pirry - Comidas Rápidas')</title>
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@stack('styles')
</head>
<body>

<nav>
  <div class="logo">El Pirry</div>
  <button class="nav-toggle" id="navToggle" aria-label="Menú">☰</button>
  <ul id="navMenu">
    <li><a href="{{ route('home') }}">Inicio</a></li>
    <li><a href="{{ route('home') }}#Productos">Menú</a></li>
    <li><a href="{{ route('home') }}#Nosotros">Nosotros</a></li>
    <li class="nav-cart">
      <a href="#" onclick="verCarrito(); return false;" class="cart-nav-btn" title="Ver carrito">🛒 <span id="navCartCount">0</span></a>
    </li>
    <li>
      @auth
        <span class="user-greet">Hola, {{ Auth::user()->nombre }}</span>
        @if(Auth::user()->isAdmin())
          <a href="{{ route('admin.dashboard') }}" class="btn-primary">Panel Admin</a>
        @endif
        <form action="{{ route('logout') }}" method="POST" style="display:inline">
          @csrf
          <button type="submit" class="btn-primary">Cerrar sesión</button>
        </form>
      @else
        <a href="{{ route('login') }}" class="btn-primary">Iniciar sesión</a>
      @endauth
    </li>
  </ul>
</nav>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-error">{{ session('error') }}</div>
@endif

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
const isAdmin = {{ ($esAdmin ?? false) ? 'true' : 'false' }};
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const routes = {
  productos: '{{ url('/productos') }}',
  carritoAdd: '{{ route('carrito.add') }}',
  carritoShow: '{{ route('carrito.show') }}',
  carritoUpdate: '{{ route('carrito.update') }}',
  carritoRemove: '{{ route('carrito.remove') }}',
  carritoConfirm: '{{ route('carrito.confirm') }}',
  login: '{{ route('login') }}',
};
</script>
<script src="{{ asset('js/index.js') }}"></script>
@stack('scripts')
</body>
</html>
