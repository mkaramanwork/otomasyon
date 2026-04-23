
<?php $__env->startSection('title','Ürün Düzenle'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4 class="mb-4">Ürün Düzenle</h4>

    <form action="<?php echo e(route('lab_products.update',$labProduct->id)); ?>" method="POST">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

        <div class="form-group mb-3">
            <label>Ürün Kodu</label>
            <input type="text" name="product_code" value="<?php echo e($labProduct->product_code); ?>" class="form-control">
        </div>

        <div class="form-group mb-3">
            <label>Ürün Adı</label>
            <input type="text" name="product_name" value="<?php echo e($labProduct->product_name); ?>" class="form-control">
        </div>

        <h5 class="mt-4">Parametreler</h5>
        <?php $__currentLoopData = $labProduct->parameters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $param): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card mb-2">
                <div class="card-header">
                    <?php echo e($param->parameter->parameter); ?> <small class="text-muted">(<?php echo e($param->parameter->method_tr); ?>)</small>
                </div>
                <div class="card-body row">
                    <div class="col-md-4">
                        <input type="text" name="parameters[<?php echo e($param->id); ?>][min]" value="<?php echo e($param->min_value); ?>" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="parameters[<?php echo e($param->id); ?>][max]" value="<?php echo e($param->max_value); ?>" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="parameters[<?php echo e($param->id); ?>][net]" value="<?php echo e($param->net_value); ?>" class="form-control">
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <button type="submit" class="btn btn-success mt-3">Güncelle</button>
        <a href="<?php echo e(route('lab_products.index')); ?>" class="btn btn-secondary mt-3">Listeye Dön</a>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/lab_products/edit.blade.php ENDPATH**/ ?>