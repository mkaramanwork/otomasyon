

<?php $__env->startSection('title','Ürün Sicil Kartları'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mt-4">
    <h3>📦 Ürün Sicil Kartları</h3>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>#</th>
                <th>Ürün Adı</th>
                <th>Kod</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($index + 1); ?></td>
                    <td><?php echo e($product->name); ?></td>
                    <td><?php echo e($product->code); ?></td>
                    <td>
                        <a href="<?php echo e(route('urunAgaci.show',$product->id)); ?>" class="btn btn-primary btn-sm">
                            Detay Göster
                        </a>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/urun_agaci/index.blade.php ENDPATH**/ ?>