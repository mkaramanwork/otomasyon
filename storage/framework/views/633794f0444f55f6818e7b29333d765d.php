
<?php $__env->startSection('title','Ürün Detayı'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4 class="mb-4">Ürün Detayı</h4>

    <ul class="list-group mb-4">
        <li class="list-group-item"><strong>Ürün Kodu:</strong> <?php echo e($labProduct->product_code); ?></li>
        <li class="list-group-item"><strong>Ürün Adı:</strong> <?php echo e($labProduct->product_name); ?></li>
    </ul>

    <h5>Parametreler</h5>
    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>Parametre</th>
                <th>Metot (TR)</th>
                <th>Min</th>
                <th>Max</th>
                <th>Net</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $labProduct->parameters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $param): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($param->parameter->parameter ?? '-'); ?></td>
                    <td><?php echo e($param->parameter->method_tr ?? '-'); ?></td>
                    <td><?php echo e($param->min_value); ?></td>
                    <td><?php echo e($param->max_value); ?></td>
                    <td><?php echo e($param->net_value); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="mt-3">
        <a href="<?php echo e(route('lab_products.edit',$labProduct->id)); ?>" class="btn btn-warning">Düzenle</a>
        <a href="<?php echo e(route('lab_products.index')); ?>" class="btn btn-secondary">Listeye Dön</a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/lab_products/show.blade.php ENDPATH**/ ?>