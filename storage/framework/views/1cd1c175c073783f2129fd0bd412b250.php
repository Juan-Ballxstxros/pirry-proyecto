<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<title><?php echo $__env->yieldContent('title', 'El Pirry - Comidas Rápidas'); ?></title>
<link rel="stylesheet" href="<?php echo e(asset('css/index.css')); ?>">
<?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>

<nav>
  <div class="logo">El Pirry</div>
  <button class="nav-toggle" id="navToggle" aria-label="Menú">☰</button>
  <ul id="navMenu">
    <li><a href="<?php echo e(route('home')); ?>">Inicio</a></li>
    <li><a href="<?php echo e(route('home')); ?>#Productos">Menú</a></li>
    <li><a href="<?php echo e(route('home')); ?>#Nosotros">Nosotros</a></li>
    <li class="nav-cart">
      <a href="#" onclick="verCarrito(); return false;" class="cart-nav-btn" title="Ver carrito">🛒 <span id="navCartCount">0</span></a>
    </li>
    <li>
      <?php if(auth()->guard()->check()): ?>
        <span class="user-greet">Hola, <?php echo e(Auth::user()->nombre); ?></span>
        <?php if(Auth::user()->isAdmin()): ?>
          <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn-primary">Panel Admin</a>
        <?php endif; ?>
        <form action="<?php echo e(route('logout')); ?>" method="POST" style="display:inline">
          <?php echo csrf_field(); ?>
          <button type="submit" class="btn-primary">Cerrar sesión</button>
        </form>
      <?php else: ?>
        <a href="<?php echo e(route('login')); ?>" class="btn-primary">Iniciar sesión</a>
      <?php endif; ?>
    </li>
  </ul>
</nav>

<?php if(session('success')): ?>
  <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
  <div class="alert alert-error"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<?php echo $__env->yieldContent('content'); ?>

<footer>
  <div class="footer-content">
    <div class="footer-box">
      <h3>El Pirry</h3>
      <p>Las mejores salchipapas y hamburguesas de Guayabal 🍔🔥</p>
    </div>
    <div class="footer-box">
      <h3>Enlaces</h3>
      <ul>
        <li><a href="<?php echo e(route('home')); ?>">Inicio</a></li>
        <li><a href="<?php echo e(route('home')); ?>#Productos">Menú</a></li>
        <li><a href="<?php echo e(route('home')); ?>#Nosotros">Nosotros</a></li>
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
  <p class="footer-copy">© <?php echo e(date('Y')); ?> El Pirry - Todos los derechos reservados</p>
</footer>

<div id="carrito-badge" onclick="verCarrito()">
  🛒 <span id="countCarrito">0</span>
</div>

<script>
const isAdmin = <?php echo e(($esAdmin ?? false) ? 'true' : 'false'); ?>;
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const routes = {
  productos: '<?php echo e(url('/productos')); ?>',
  carritoAdd: '<?php echo e(route('carrito.add')); ?>',
  carritoShow: '<?php echo e(route('carrito.show')); ?>',
  carritoUpdate: '<?php echo e(route('carrito.update')); ?>',
  carritoRemove: '<?php echo e(route('carrito.remove')); ?>',
  carritoConfirm: '<?php echo e(route('carrito.confirm')); ?>',
  login: '<?php echo e(route('login')); ?>',
};
</script>
<script src="<?php echo e(asset('js/index.js')); ?>"></script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\JUAN DAVID\Downloads\pirry-proyecto-master\pirry-proyecto-master\resources\views/layouts/store.blade.php ENDPATH**/ ?>