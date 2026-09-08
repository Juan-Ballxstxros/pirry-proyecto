<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Recuperar Contraseña - El Pirry</title>
<link rel="stylesheet" href="<?php echo e(asset('css/login.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('css/toast.css')); ?>">
</head>
<body>

<div class="contenedor">
    <div class="left">
        <div class="texto">
            <h1>El Pirry</h1>
            <p>Te ayudamos a recuperar tu acceso</p>
        </div>
    </div>

    <div class="right">
        <form action="<?php echo e(route('password.email')); ?>" method="POST" class="login-box">
            <?php echo csrf_field(); ?>
            <h2>Recuperar Contraseña</h2>

            <?php if($errors->any()): ?>
                <div class="alert-error">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p><?php echo e($error); ?></p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <div class="input-box">
                <label>Correo electrónico</label>
                <input type="email" name="correo" value="<?php echo e(old('correo')); ?>" required>
            </div>

            <div class="botones">
                <button type="submit" class="btn ingresar">Enviar enlace</button>
                <a href="<?php echo e(route('login')); ?>" class="btn volver">Volver al login</a>
                <p class="register-link">
                    Recibirás un enlace temporal para crear una nueva contraseña.
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
<?php /**PATH C:\Users\JUAN DAVID\Downloads\pirry-proyecto-master\pirry-proyecto-master\resources\views/auth/forgot.blade.php ENDPATH**/ ?>