<?php $__env->startSection('title', 'Asignar Permisos'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/as_permisos.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<h1>Asignar Permisos a Roles</h1>
<div class="card">
    <form method="POST" action="<?php echo e(route('admin.permisos.assign.post')); ?>">
        <?php echo csrf_field(); ?>
        <label>Rol</label>
        <select name="id_roles" required>
            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($rol->id_roles); ?>"><?php echo e($rol->nombre); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <label>Permiso</label>
        <select name="id_permisos" required>
            <?php $__currentLoopData = $permisos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($p->id_permisos); ?>"><?php echo e($p->nombre); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <button type="submit">Asignar</button>
    </form>
</div>

<div class="card">
    <h3>Asignaciones actuales</h3>
    <table>
        <tr><th>Rol ID</th><th>Permiso ID</th></tr>
        <?php $__currentLoopData = $asignados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr><td><?php echo e($a->id_roles); ?></td><td><?php echo e($a->id_permisos); ?></td></tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\JUAN DAVID\Downloads\pirry-proyecto-master\pirry-proyecto-master\resources\views/admin/permisos/assign.blade.php ENDPATH**/ ?>