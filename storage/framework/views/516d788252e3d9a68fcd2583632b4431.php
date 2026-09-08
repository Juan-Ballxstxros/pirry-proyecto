<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $__env->yieldContent('title', 'Admin - El Pirry'); ?></title>
<link rel="stylesheet" href="<?php echo e(asset('css/interfaz_ad.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('css/toast.css')); ?>">
<?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>

<div class="dashboard">
    <aside class="sidebar">
        <h2 class="logo">Admin</h2>
        <ul>
            <li class="<?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                <a href="<?php echo e(route('admin.dashboard')); ?>">Inicio</a>
            </li>
            <li class="<?php echo e(request()->routeIs('admin.usuarios.*') ? 'active' : ''); ?>">
                <a href="<?php echo e(route('admin.usuarios.index')); ?>">Usuarios</a>
            </li>
            <li class="<?php echo e(request()->routeIs('admin.roles.*') ? 'active' : ''); ?>">
                <a href="<?php echo e(route('admin.roles.index')); ?>">Roles</a>
            </li>
            <li class="<?php echo e(request()->routeIs('admin.permisos.*') ? 'active' : ''); ?>">
                <a href="<?php echo e(route('admin.permisos.index')); ?>">Permisos</a>
            </li>
            <li>
                <a href="<?php echo e(route('home')); ?>">Ver Tienda</a>
            </li>
            <li class="logout">
                <form action="<?php echo e(route('logout')); ?>" method="POST" onsubmit="return confirmLogout(this)">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="sidebar-logout">Cerrar sesión</button>
                </form>
            </li>
        </ul>
    </aside>

    <main class="main">
        <?php echo $__env->yieldContent('content'); ?>
    </main>
</div>

<script>
// Mensajes flash de Laravel -> toasts bonitos
window.flashMessages = window.flashMessages || [];
<?php if(session('success')): ?>
  window.flashMessages.push({ type: 'success', message: <?php echo json_encode(session('success'), 15, 512) ?> });
<?php endif; ?>
<?php if(session('error')): ?>
  window.flashMessages.push({ type: 'error', message: <?php echo json_encode(session('error'), 15, 512) ?> });
<?php endif; ?>
</script>
<script src="<?php echo e(asset('js/toast.js')); ?>"></script>

</body>
</html>
<?php /**PATH C:\Users\JUAN DAVID\Downloads\pirry-proyecto-master\pirry-proyecto-master\resources\views/layouts/admin.blade.php ENDPATH**/ ?>