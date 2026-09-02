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

    <label>Imagen del producto</label>
    <input type="file" id="prodImagen" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
    <div class="imagen-preview" id="imagenPreview">
      <p class="imagen-vacia" id="imagenVaciaTxt">Sin imagen</p>
      <img id="imagenPreviewImg" src="" alt="Vista previa de la imagen del producto" style="display:none">
    </div>
    <button type="button" class="btn-eliminar-imagen" id="btnEliminarImagen" onclick="eliminarImagenProducto()" style="display:none">🗑️ Eliminar imagen</button>

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
    {{-- Botón de envío del pedido por WhatsApp. El número se configura en
         .env → WHATSAPP_NUMERO_NEGOCIO. Solo LEE el carrito existente:
         no modifica productos, cantidades, precios ni contadores. --}}
    <button type="button" class="btn-whatsapp" onclick="enviarPedidoWhatsApp()">
      <svg viewBox="0 0 32 32" aria-hidden="true" focusable="false">
        <path fill="currentColor" d="M16.004 3.2c-7.06 0-12.8 5.74-12.8 12.8 0 2.26.6 4.46 1.72 6.4L3.2 28.8l6.56-1.68a12.75 12.75 0 0 0 6.24 1.6h.004c7.06 0 12.8-5.74 12.8-12.8s-5.74-12.8-12.8-12.8zm0 23.392h-.004c-1.92 0-3.8-.52-5.44-1.5l-.39-.23-4.04 1.04 1.08-3.94-.26-.4a10.6 10.6 0 0 1-1.63-5.66c0-5.87 4.78-10.64 10.65-10.64 2.84 0 5.51 1.11 7.52 3.12a10.57 10.57 0 0 1 3.12 7.53c0 5.87-4.78 10.64-10.65 10.64zm5.84-7.97c-.32-.16-1.89-.93-2.18-1.04-.29-.11-.5-.16-.72.16-.21.32-.83 1.04-1.02 1.25-.19.21-.37.24-.69.08-.32-.16-1.35-.5-2.57-1.59-.95-.85-1.59-1.9-1.78-2.22-.19-.32-.02-.49.14-.65.14-.14.32-.37.48-.56.16-.19.21-.32.32-.53.11-.21.05-.4-.03-.56-.08-.16-.72-1.73-.98-2.37-.26-.62-.52-.54-.72-.55h-.61c-.21 0-.56.08-.85.4-.29.32-1.11 1.09-1.11 2.65s1.14 3.07 1.3 3.29c.16.21 2.24 3.42 5.42 4.79.76.33 1.35.52 1.81.67.76.24 1.45.21 2 .13.61-.09 1.89-.77 2.15-1.52.27-.74.27-1.38.19-1.52-.08-.13-.29-.21-.61-.37z"/>
      </svg>
      Enviar pedido por WhatsApp
    </button>

    <div class="modal-buttons">
      <button class="btn-cancel" onclick="cerrarCarrito()">Seguir comprando</button>
      <button class="btn-save" onclick="confirmarPedido()">✅ Confirmar Pedido</button>
    </div>
  </div>
</div>

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

@push('scripts')
<script src="{{ asset('js/menu.js') }}"></script>
@endpush
