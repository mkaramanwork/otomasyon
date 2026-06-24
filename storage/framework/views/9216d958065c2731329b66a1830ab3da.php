

<?php $__env->startSection('title', 'Ham Maddeler Listesi'); ?>

<?php $__env->startSection('content'); ?>
<?php if(session('success')): ?>
    <div id="success-alert" class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <?php echo e(session('success')); ?>

        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<div class="row mb-3">
    <div class="col-md-6">
        <h4>Ham Madde Listesi</h4>
    </div>
    <div class="col-md-6 text-right">
        <a href="<?php echo e(route('raw-materials.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Ekle
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-striped table-hover mb-0">
            <thead class="thead-dark">
                <tr>
                    <th>Ad</th>
                    <th>Birim</th>
                    <th>Stok (kg)</th>
                    <th>Fiyat (TL)</th>
                    <th>Fiyat (USD)</th>
                    <th>Fiyat (EUR)</th>
                    <th>Aktif Para Birimi</th>
                    <th>Katkı Dokümanı</th>
                    <th>Kullanım Oranı (%)</th>
                    <th>Performans Seviyesi</th>
                    <th>Rakip Ürün</th>
                    <th>Ekleyen</th>
                    <th class="text-right">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $rawMaterials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($material->name); ?></td>
                        <td><?php echo e($material->unit); ?></td>
                        <td><?php echo e(number_format($material->stock_kg, 2, ',', '.')); ?></td>
                        <td><?php echo e(number_format($material->unit_price_tl, 2, ',', '.')); ?></td>
                        <td><?php echo e(number_format($material->unit_price_usd, 2, ',', '.')); ?></td>
                        <td><?php echo e(number_format($material->unit_price_eur, 2, ',', '.')); ?></td>
                        <td><?php echo e($material->currency); ?></td>
                        <td>
                            <?php if($material->semi_doc): ?>
                                <a href="<?php echo e(asset('storage/'.$material->semi_doc)); ?>" target="_blank" class="btn btn-sm btn-info">PDF Görüntüle</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($material->usage_rate ?? '-'); ?></td>
                        <td><?php echo e($material->performance_level ?? '-'); ?></td>
                        <td><?php echo e($material->competitor_product ?? '-'); ?></td>
                        <td><?php echo e($material->addedBy->name ?? '-'); ?></td>
                        <td class="text-right">
                            <a href="<?php echo e(route('raw-materials.edit', $material->id)); ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo e(route('raw-materials.destroy', $material->id)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Silmek istediğine emin misin?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="13" class="text-center text-muted">Kayıtlı ham madde bulunamadı.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    <?php echo e($rawMaterials->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/rawmaterials/index.blade.php ENDPATH**/ ?>