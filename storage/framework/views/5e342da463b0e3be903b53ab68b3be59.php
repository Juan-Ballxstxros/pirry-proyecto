<?php $__env->startSection('title', 'Agregar Permiso'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/ag_permisos.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<h1>Agregar Permiso</h1>
<div class="card">
    <form method="POST" action="<?php echo e(route('admin.permisos.store')); ?>">
        <?php echo csrf_field(); ?>
        <label>Nombre</label>
        <input type="text" name="nombre" required>
        <label>Descripción</label>
        <textarea name="descripcion" required></textarea>
        <button type="submit">Guardar</button>
        <a href="<?php echo e(route('admin.permisos.index')); ?>">Cancelar</a>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\JUAN DAVID\Downloads\pirry-proyecto-master\pirry-proyecto-master\resources\views/admin/permisos/create.blade.php ENDPATH**/ ?>