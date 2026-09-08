<?php $__env->startSection('title', 'Usuarios'); ?>

<?php $__env->startSection('content'); ?>
<div class="topbar">
    <h1>Usuarios Registrados</h1>
    <a class="btn-crear" href="<?php echo e(route('admin.usuarios.create')); ?>">+ Registrar</a>
</div>

<div class="card">
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Usuario</th>
            <th>Rol</th>
            <th>Estado</th>
            <th>Acción</th>
        </tr>
        <?php $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($u->id); ?></td>
            <td><?php echo e($u->nombre); ?></td>
            <td><?php echo e($u->usuario); ?></td>
            <td><?php echo e($u->rol?->nombre ?? $u->id_rol); ?></td>
            <td class="<?php echo e(strtolower($u->estado)); ?>"><?php echo e($u->estado); ?></td>
            <td>
                <form action="<?php echo e(route('admin.usuarios.toggle', $u)); ?>" method="POST" style="display:inline">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <button type="submit" class="btn <?php echo e(strtolower($u->estado) === 'activo' ? 'desactivar' : 'activar'); ?>">
                        <?php echo e(strtolower($u->estado) === 'activo' ? 'Inhabilitar' : 'Habilitar'); ?>

                    </button>
                </form>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\JUAN DAVID\Downloads\pirry-proyecto-master\pirry-proyecto-master\resources\views/admin/usuarios/index.blade.php ENDPATH**/ ?>