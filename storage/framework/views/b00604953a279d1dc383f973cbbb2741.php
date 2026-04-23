
<?php $__env->startSection('title','Yeni Ürün Ekle'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4 class="mb-4">Yeni Ürün Ekle</h4>

    <form action="<?php echo e(route('lab_products.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="form-group mb-3">
            <label>Ürün Kodu</label>
            <input type="text" name="product_code" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label>Ürün Adı</label>
            <input type="text" name="product_name" class="form-control" required>
        </div>

        <h5 class="mt-4">Parametreler</h5>
        <?php $__currentLoopData = $parameters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parameter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card mb-2">
                <div class="card-header">
                    <?php echo e($parameter->parameter); ?> <small class="text-muted">(<?php echo e($parameter->method_tr); ?>)</small>
                </div>
                <div class="card-body row">
                    <div class="col-md-4">
                        <input type="text" name="parameters[<?php echo e($parameter->id); ?>][min]" placeholder="Min" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="parameters[<?php echo e($parameter->id); ?>][max]" placeholder="Max" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="parameters[<?php echo e($parameter->id); ?>][net]" placeholder="Net" class="form-control">
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <button type="submit" class="btn btn-success mt-3">Kaydet</button>
        <a href="<?php echo e(route('lab_products.index')); ?>" class="btn btn-secondary mt-3">Listeye Dön</a>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/lab_products/create.blade.php ENDPATH**/ ?>