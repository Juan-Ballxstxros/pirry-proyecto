@extends('layouts.store')

@section('title', 'El Pirry - Comidas Rápidas')

@section('content')

<section class="hero" id="inicio">
  <div class="hero-text">
    <h1>¡Sabor y calidad en cada bocados!</h1>
    <p>En El Pirry preparamos las mejores salchipapas y hamburguesas de todo Guayabal.</p>
    <div class="hero-buttons">
      <a href="#Productos" class="btn-orange">Ver Menú Completo</a>
      <a href="#" onclick="verCarrito(); return false;" class="btn-outline">🛒 Mi Pedido</a>
    </div>
  </div>
  <img src="https://images.unsplash.com/photo-1550547660-d9450f859349" alt="Comida">
</section>

<section id="Productos" class="section">
  <h2 class="menu-titulo">Menú</h2>
  <p class="menu-subtitulo">Elige una categoría y agrega lo que más te guste</p>

  {{-- ============================================================
       MENÚ + CRUD admin: componente Vue (MenuProductos.vue) cargado
       por Vite. Usa las MISMAS clases CSS (menu.css / index.css), las
       MISMAS rutas API de Laravel y el MISMO token CSRF. El buscador,
       los filtros por categoría, la cuadrícula y el modal de producto
       viven dentro del componente. Los scripts clásicos index.js y
       menu.js ya no se cargan en esta página (los archivos se conservan
       en public/js como referencia).
       ============================================================ --}}
  <div id="vue-menu"></div>

  {{-- ============================================================
       MENÚ ANTERIOR (conectado a la base de datos) — TEMPORALMENTE
       OCULTO porque el menú por categorías de arriba ya está conectado
       a la base de datos. Este bloque (lista plana + búsqueda en
       servidor) se conserva por si se quiere volver a mostrar.
       NO ELIMINAR.
       ============================================================
  <div class="products-filters">
    <input type="text" id="busqueda" placeholder="🔍 Buscar producto por nombre...">
    <select id="filtroCategoria">
      <option value="">Todas las categorías</option>
    </select>
    @if($esAdmin)
      <button class="btn-edit" onclick="abrirModalCrear()">➕ Nuevo Producto</button>
    @endif
  </div>

  <div class="products" id="listaProductos">
    <p class="loading-msg">Cargando menú...</p>
  </div>
  ============================================================ --}}
</section>

{{-- El modal de producto (CRUD admin) vive ahora dentro del componente
    Vue MenuProductos.vue, con el mismo HTML y las mismas clases. --}}

{{-- El modal del carrito vive ahora en el componente Vue CarritoModal.vue
    (mismo HTML, mismas clases, mismas rutas y la misma validación de
    sesión del lado del servidor: 401 → modalAuth de Blade). --}}
<div id="vue-carrito"></div>

{{-- Modal de autenticación: aparece si un usuario sin sesión intenta
     confirmar el pedido o enviarlo por WhatsApp. Usa las páginas de
     login/registro que ya existen; el carrito se conserva en la sesión
     y sigue disponible después de iniciar sesión. --}}
<div id="modalAuth" class="modal">
  <div class="modal-content modal-auth-content">
    <div class="modal-auth-icon" aria-hidden="true">🔒</div>
    <h3>Debes iniciar sesión para continuar</h3>
    <p class="modal-auth-texto">Para confirmar tu pedido necesitas iniciar sesión o crear una cuenta.</p>
    <div class="modal-auth-buttons">
      <a href="{{ route('login') }}" class="btn-auth-login">Iniciar sesión</a>
      <a href="{{ route('register') }}" class="btn-auth-register">Crear cuenta</a>
    </div>
  </div>
</div>

<section id="Nosotros" class="section">
  <h2>¿Por qué elegir El Pirry?</h2>
  <div class="stats">
    <div class="stat"><h3>8+</h3><p>Años de experiencia</p></div>
    <div class="stat"><h3>15k+</h3><p>Clientes felices</p></div>
    <div class="stat"><h3>20+</h3><p>Productos en menú</p></div>
    <div class="stat"><h3>4.9</h3><p>Rating promedio</p></div>
  </div>
</section>

@endsection

{{-- Estilos y script del nuevo menú por categorías --}}
@push('styles')
<link rel="stylesheet" href="{{ asset('css/menu.css') }}">
@endpush

{{-- menu.js ya no se carga en esta página: el menú lo renderiza
    MenuProductos.vue (el archivo se conserva en public/js). --}}
