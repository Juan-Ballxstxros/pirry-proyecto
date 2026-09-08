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

  
  <div id="vue-menu"></div>

  
</section>




<div id="vue-carrito"></div>


<div id="modalAuth" class="modal">
  <div class="modal-content modal-auth-content">
    <div class="modal-auth-icon" aria-hidden="true">🔒</div>
    <h3>Debes iniciar sesión para continuar</h3>
    <p class="modal-auth-texto">Para confirmar tu pedido necesitas iniciar sesión o crear una cuenta.</p>
    <div class="modal-auth-buttons">
      <a href="<?php echo e(route('login')); ?>" class="btn-auth-login">Iniciar sesión</a>
      <a href="<?php echo e(route('register')); ?>" class="btn-auth-register">Crear cuenta</a>
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



<?php echo $__env->make('layouts.store', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\JUAN DAVID\Downloads\pirry-proyecto-master\pirry-proyecto-master\resources\views/store/index.blade.php ENDPATH**/ ?>