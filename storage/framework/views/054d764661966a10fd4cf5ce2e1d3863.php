<?php $__env->startSection('title', 'El Pirry - Comidas Rápidas'); ?>

<?php $__env->startSection('content'); ?>

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

  
  <div class="menu-busqueda">
    <input type="text" id="busquedaMenu" placeholder="🔍 Buscar producto por nombre...">
  </div>

  
  <div class="menu-filtros" id="menuFiltros">
    <p class="loading-msg">Cargando categorías...</p>
  </div>

  <div class="menu-grid" id="menuGrid">
    <p class="loading-msg">Cargando productos...</p>
  </div>

  <?php if($esAdmin): ?>
    
    <div class="menu-admin-bar">
      <button class="btn-edit" onclick="abrirModalCrear()">➕ Nuevo Producto</button>
    </div>
  <?php endif; ?>

  
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

    <?php if($esAdmin): ?>
      <p class="id-info"><strong>ID del producto:</strong> <span id="prodIdDisplay"></span> (no modificable)</p>
    <?php endif; ?>

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

<?php $__env->stopSection(); ?>


<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/menu.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/menu.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\JUAN DAVID\Downloads\pirry-proyecto-master\pirry-proyecto-master\resources\views/store/index.blade.php ENDPATH**/ ?>