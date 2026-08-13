<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Iniciar Sesión - El Pirry</title>
<link rel="stylesheet" href="<?php echo e(asset('css/login.css')); ?>">
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
                <p class="info-login">Si olvidó su contraseña, contacte al administrador del sistema.</p>
            </div>
        </form>
    </div>
</div>

</body>
</html>
<?php /**PATH C:\Users\JUAN DAVID\Downloads\pirry-proyecto-master\pirry-proyecto-master\resources\views/auth/login.blade.php ENDPATH**/ ?>