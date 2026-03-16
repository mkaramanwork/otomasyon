
<?php $__env->startSection('title','Yeni Personel'); ?>
<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>➕ Yeni Personel</h4>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <li><?php echo e($error); ?></li> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('personnel.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="mb-3">
            <label>Adı</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Ünvanı</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Görevi</label>
            <input type="text" name="duty" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>İşe Başlama Tarihi</label>
            <input type="date" name="start_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Vekili</label>
            <input type="text" name="deputy" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Kaydet</button>
        <a href="<?php echo e(route('personnel.index')); ?>" class="btn btn-secondary">Geri</a>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/personnel/create.blade.php ENDPATH**/ ?>