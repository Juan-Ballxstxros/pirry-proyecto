<?php $__env->startSection('title', 'Registrar Usuario'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/registro.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="header">
    <h1>Registrar Usuario</h1>
    <a href="<?php echo e(route('admin.usuarios.index')); ?>" class="btn-volver">← Volver</a>
</div>

<div class="contenedor-form">
    <div class="card">
        <form action="<?php echo e(route('admin.usuarios.store')); ?>" method="POST" class="formulario">
            <?php echo csrf_field(); ?>
            <div class="grid">
                <div class="campo">
                    <label>Nombres</label>
                    <input type="text" name="nombre" value="<?php echo e(old('nombre')); ?>" required>
                </div>
                <div class="campo">
                    <label>Documento</label>
                    <input type="text" name="documento" value="<?php echo e(old('documento')); ?>" required>
                </div>
                <div class="campo">
                    <label>Correo</label>
                    <input type="email" name="correo" value="<?php echo e(old('correo')); ?>" required>
                </div>
                <div class="campo">
                    <label>Usuario</label>
                    <input type="text" name="usuario" value="<?php echo e(old('usuario')); ?>" required>
                </div>
                <div class="campo">
                    <label>Contraseña</label>
                    <input type="password" name="contrasena" required>
                </div>
                <div class="campo">
                    <label>Rol</label>
                    <select name="id_rol" required>
                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($rol->id_roles); ?>"><?php echo e($rol->nombre); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="campo">
                    <label>Estado</label>
                    <select name="estado" required>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="acciones">
                <button type="submit" class="btn guardar">Registrar</button>
                <a href="<?php echo e(route('admin.usuarios.index')); ?>" class="btn cancelar">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\JUAN DAVID\Downloads\pirry-proyecto-master\pirry-proyecto-master\resources\views/admin/usuarios/create.blade.php ENDPATH**/ ?>