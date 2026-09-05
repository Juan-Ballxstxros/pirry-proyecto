<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Iniciar Sesión - El Pirry</title>
<link rel="stylesheet" href="<?php echo e(asset('css/login.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('css/toast.css')); ?>">
</head>
<body>

<div class="contenedor">
    <div class="left">
        <div class="texto">
            <h1>Bienvenido</h1>
            <p>Accede al sistema de gestión</p>
        </div>
    </div>

    <div class="right">
        <form action="<?php echo e(route('login.post')); ?>" method="POST" class="login-box">
            <?php echo csrf_field(); ?>
            <h2>Iniciar Sesión</h2>

            <?php if($errors->any()): ?>
                <div class="alert-error">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p><?php echo e($error); ?></p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <div class="input-box">
                <label>Correo</label>
                <input type="email" name="correo" value="<?php echo e(old('correo')); ?>" required>
            </div>

            <div class="input-box">
                <label>Contraseña</label>
                <input type="password" name="contrasena" required>
            </div>

            <div class="botones">
                <button type="submit" class="btn ingresar">Ingresar</button>
                <a href="<?php echo e(route('home')); ?>" class="btn volver">Volver</a>
                <p class="register-link">
                    ¿No tienes una cuenta? <a href="<?php echo e(route('register')); ?>">Crear cuenta</a>
                </p>
                <p class="register-link">
                    ¿Olvidaste tu contraseña? <a href="<?php echo e(route('password.request')); ?>">Recupérala aquí</a>
                </p>
            </div>
        </form>
    </div>
</div>

<script>
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
<?php /**PATH C:\Users\JUAN DAVID\Downloads\pirry-proyecto-master\pirry-proyecto-master\resources\views/auth/login.blade.php ENDPATH**/ ?>