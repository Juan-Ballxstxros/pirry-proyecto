<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Crear Cuenta - El Pirry</title>
<link rel="stylesheet" href="<?php echo e(asset('css/login.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('css/toast.css')); ?>">
</head>
<body>

<div class="contenedor">
    <div class="left">
        <div class="texto">
            <h1>Únete a El Pirry</h1>
            <p>Crea tu cuenta y disfruta de nuestro menú</p>
        </div>
    </div>

    <div class="right">
        <form action="<?php echo e(route('register.post')); ?>" method="POST" class="login-box register-box">
            <?php echo csrf_field(); ?>
            <h2>Crear Cuenta</h2>

            <?php if($errors->any()): ?>
                <div class="alert-error">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p><?php echo e($error); ?></p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <div class="input-box">
                <label>Nombre completo</label>
                <input type="text" name="nombre" value="<?php echo e(old('nombre')); ?>" required>
            </div>

            <div class="input-box">
                <label>Documento</label>
                <input type="text" name="documento" value="<?php echo e(old('documento')); ?>" required>
            </div>

            <div class="input-box">
                <label>Correo electrónico</label>
                <input type="email" name="correo" value="<?php echo e(old('correo')); ?>" required>
            </div>

            <div class="input-box">
                <label>Usuario</label>
                <input type="text" name="usuario" value="<?php echo e(old('usuario')); ?>" required>
            </div>

            <div class="input-box">
                <label>Contraseña</label>
                <input type="password" name="contrasena" required minlength="6">
            </div>

            <div class="input-box">
                <label>Confirmar contraseña</label>
                <input type="password" name="contrasena_confirmation" required minlength="6">
            </div>

            <div class="botones">
                <button type="submit" class="btn ingresar">Registrarse</button>
                <a href="<?php echo e(route('login')); ?>" class="btn volver">Ya tengo cuenta</a>
                <a href="<?php echo e(route('home')); ?>" class="btn volver">Volver al inicio</a>
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
<?php /**PATH C:\Users\JUAN DAVID\Downloads\pirry-proyecto-master\pirry-proyecto-master\resources\views/auth/register.blade.php ENDPATH**/ ?>