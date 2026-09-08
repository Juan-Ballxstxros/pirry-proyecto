<?php $__env->startSection('title', 'Panel Administrador'); ?>

<?php $__env->startSection('content'); ?>
<div class="topbar">
    <div class="user">Bienvenido <?php echo e(Auth::user()->nombre); ?></div>
</div>

<div class="cards">
    <div class="card">Usuarios<br><strong><?php echo e($totalUsuarios); ?></strong></div>
    <div class="card">Roles<br><strong><?php echo e($totalRoles); ?></strong></div>
    <div class="card">Permisos<br><strong><?php echo e($totalPermisos); ?></strong></div>
    <div class="card">Activos<br><strong><?php echo e($porcentaje); ?>%</strong></div>
</div>

<div class="tabla">
    <h3>Últimos usuarios</h3>
    <table>
        <tr>
            <th>Usuario</th>
            <th>Estado</th>
        </tr>
        <?php $__empty_1 = true; $__currentLoopData = $ultimos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
            <td><?php echo e($u->nombre); ?></td>
            <td class="<?php echo e(strtolower($u->estado) === 'activo' ? 'activo' : 'pendiente'); ?>"><?php echo e(ucfirst($u->estado)); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="2">No hay datos</td></tr>
        <?php endif; ?>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\JUAN DAVID\Downloads\pirry-proyecto-master\pirry-proyecto-master\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>