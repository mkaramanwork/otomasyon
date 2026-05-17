
<?php $__env->startSection('title','Soru Listesi'); ?>
<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>📑 Soru Listesi</h4>
        <a href="<?php echo e(route('questions.create')); ?>" class="btn btn-primary">Yeni Soru</a>
    </div>
    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Soru</th>
                <th>Kategori</th>
                <th>Zorluk</th>
                <th>Doğru Cevap</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($q->question_text); ?></td>
                <td><?php echo e($q->category->name); ?></td>
                <td><?php echo e($q->difficulty); ?></td>
                <td><?php echo e($q->correct_option); ?></td>
                <td>
                    <a href="<?php echo e(route('questions.edit',$q->id)); ?>" class="btn btn-warning btn-sm">Güncelle</a>
                    <form action="<?php echo e(route('questions.destroy',$q->id)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Silmek istediğine emin misin?')">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="5" class="text-center">Henüz soru yok</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/questions/index.blade.php ENDPATH**/ ?>