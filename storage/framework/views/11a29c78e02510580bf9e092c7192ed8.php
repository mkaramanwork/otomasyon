
<?php $__env->startSection('title','Laboratuvar Ürünleri'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Laboratuvar Ürünleri Listesi</h4>
        <a href="<?php echo e(route('lab_products.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Ürün Ekle
        </a>
    </div>

    <?php if($labProducts->isEmpty()): ?>
        <div class="alert alert-info">Henüz ürün kaydı bulunmamaktadır.</div>
    <?php else: ?>
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Ürün Kodu</th>
                            <th>Ürün Adı</th>
                            <th>Parametre Sayısı</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $labProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($loop->iteration); ?></td>
                                <td><?php echo e($lp->product_code); ?></td>
                                <td><?php echo e($lp->product_name); ?></td>
                                <td><?php echo e($lp->parameters->count()); ?></td>
                                <td>
                                    <a href="<?php echo e(route('lab_products.show',$lp->id)); ?>" class="btn btn-info btn-sm">Göster</a>
                                    <a href="<?php echo e(route('lab_products.edit',$lp->id)); ?>" class="btn btn-warning btn-sm">Düzenle</a>
                                    <form action="<?php echo e(route('lab_products.destroy',$lp->id)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Silmek istediğine emin misin?');">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="btn btn-danger btn-sm">Sil</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/lab_products/index.blade.php ENDPATH**/ ?>