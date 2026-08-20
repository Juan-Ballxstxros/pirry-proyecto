<?php $__env->startSection('title', 'Roles'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/roles.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<h1>Gestión de Roles</h1>

<div class="card">
    <h3>Crear Nuevo Rol</h3>
    <form method="POST" action="<?php echo e(route('admin.roles.store')); ?>" class="formulario">
        <?php echo csrf_field(); ?>
        <input type="text" name="nombre" placeholder="Nombre del rol" required>
        <input type="text" name="descripcion" placeholder="Descripción" required>
        <button type="submit">Guardar</button>
    </form>
</div>

<div class="card">
    <h3>Roles Registrados</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Estado</th>
            <th>Acción</th>
        </tr>
        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($rol->id_roles); ?></td>
            <td><?php echo e($rol->nombre); ?></td>
            <td><?php echo e($rol->descripcion); ?></td>
            <td class="<?php echo e(strtolower($rol->estado)); ?>"><?php echo e($rol->estado); ?></td>
            <td>
                <form action="<?php echo e(route('admin.roles.toggle', $rol)); ?>" method="POST" style="display:inline">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <button type="submit" class="btn">Cambiar</button>
                </form>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\JUAN DAVID\Downloads\pirry-proyecto-master\pirry-proyecto-master\resources\views/admin/roles/index.blade.php ENDPATH**/ ?>