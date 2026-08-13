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
  <h2>Nuestro Menú</h2>
  <p>Descubre nuestras especialidades</p>

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
</section>

<div id="modalProducto" class="modal">
  <div class="modal-content">
    <h3 id="modalTitulo">Editar Producto</h3>
    <input type="hidden" id="prodId">

    <label>Nombre *</label>
    <input type="text" id="prodNombre" required>

    <label>Descripción</label>
    <textarea id="prodDescripcion"></textarea>

    <label>Precio * ($)</label>
    <input type="number" id="prodPrecio" step="0.01" min="0" required>

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
    <div class="carrito-total">
      <strong>Total: $<span id="totalCarrito">0.00</span></strong>
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
