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

  {{-- Búsqueda dentro del menú --}}
  <div class="menu-busqueda">
    <input type="text" id="busquedaMenu" placeholder="🔍 Buscar producto por nombre...">
  </div>

  {{-- ============================================================
       MENÚ: filtros por categoría + cuadrícula de productos,
       conectado a la base de datos (API /productos). Diseñado a
       propósito SIN imágenes y SIN precios: las tarjetas muestran
       nombre, descripción y botón "Agregar al carrito".
       ============================================================ --}}
  <div class="menu-filtros" id="menuFiltros">
    <p class="loading-msg">Cargando categorías...</p>
  </div>

  <div class="menu-grid" id="menuGrid">
    <p class="loading-msg">Cargando productos...</p>
  </div>

  @if($esAdmin)
    {{-- Se mantiene el acceso al CRUD de productos para administradores --}}
    <div class="menu-admin-bar">
      <button class="btn-edit" onclick="abrirModalCrear()">➕ Nuevo Producto</button>
    </div>
  @endif

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

<div id="modalProducto" class="modal">
  <div class="modal-content">
    <h3 id="modalTitulo">Editar Producto</h3>
    <input type="hidden" id="prodId">

    <label>Nombre *</label>
    <input type="text" id="prodNombre" required>

    <label>Descripción</label>
    <textarea id="prodDescripcion"></textarea>

    <label>Precio ($) — opcional, no se muestra al cliente</label>
    <input type="number" id="prodPrecio" step="0.01" min="0">

    <label>Categoría *</label>
    <select id="prodCategoria" required></select>

    <label class="checkbox-label">
      <input type="checkbox" id="prodDisponible" checked>
      Disponible para venta
    </label>

    @if($esAdmin)
      <p class="id-info"><strong>ID del producto:</strong> <span id="prodIdDisplay"></span> (no modificable)</p>
    @endif

    <div class="modal-buttons">
      <button class="btn-cancel" onclick="cerrarModal()">Cancelar</button>
      <button class="btn-save" onclick="guardarProducto()">💾 Guardar Cambios</button>
    </div>
  </div>
</div>

<div id="modalCarrito" class="modal">
  <div class="modal-content">
    <h3>🛒 Tu Pedido</h3>
    <div id="contenidoCarrito">
      <p>Tu carrito está vacío.</p>
    </div>
    <div class="modal-buttons">
      <button class="btn-cancel" onclick="cerrarCarrito()">Seguir comprando</button>
      <button class="btn-save" onclick="confirmarPedido()">✅ Confirmar Pedido</button>
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

@push('scripts')
<script src="{{ asset('js/menu.js') }}"></script>
@endpush
