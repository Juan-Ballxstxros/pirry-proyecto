<?php $__env->startSection('title', 'Permisos'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/permisos.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="topbar">
    <h1>Permisos</h1>
    <div>
        <a class="btn-crear" href="<?php echo e(route('admin.permisos.create')); ?>">+ Agregar</a>
        <a class="btn-crear" href="<?php echo e(route('admin.permisos.assign')); ?>">Asignar a Rol</a>
    </div>
</div>

<div class="card">
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </tr>
        <?php $__currentLoopData = $permisos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($p->id_permisos); ?></td>
            <td><?php echo e($p->nombre); ?></td>
            <td><?php echo e($p->descripcion); ?></td>
            <td>
                <a href="<?php echo e(route('admin.permisos.edit', $p)); ?>" class="btn">Editar</a>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\JUAN DAVID\Downloads\pirry-proyecto-master\pirry-proyecto-master\resources\views/admin/permisos/index.blade.php ENDPATH**/ ?>