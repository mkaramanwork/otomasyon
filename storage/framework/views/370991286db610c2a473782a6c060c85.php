
<?php $__env->startSection('title','Personel Listesi'); ?>
<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>👥 Personel Listesi</h4>
    <a href="<?php echo e(route('personnel.create')); ?>" class="btn btn-primary mb-3">➕ Yeni Personel</a>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Adı</th>
                <th>Ünvanı</th>
                <th>Görevi</th>
                <th>İşe Başlama</th>
                <th>Vekili</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $personnel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($p->name); ?></td>
                <td><?php echo e($p->title); ?></td>
                <td><?php echo e($p->duty); ?></td>
                <td><?php echo e($p->start_date); ?></td>
                <td><?php echo e($p->deputy); ?></td>
                <td>
                    <a href="<?php echo e(route('personnel.show',$p->id)); ?>" class="btn btn-info btn-sm">Detay</a>
                    <a href="<?php echo e(route('personnel.edit',$p->id)); ?>" class="btn btn-warning btn-sm">Güncelle</a>
                    <form action="<?php echo e(route('personnel.destroy',$p->id)); ?>" method="POST" style="display:inline;">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/personnel/index.blade.php ENDPATH**/ ?>