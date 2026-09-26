
<?php $__env->startSection('title','Teklifler'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4 class="mb-3">📑 Teklifler</h4>

    <!-- Yeni Teklif Oluştur Butonu -->
    <a href="<?php echo e(route('offers.create')); ?>" class="btn btn-success mb-3">
        ➕ Yeni Teklif Oluştur
    </a>

    <!-- Teklifler Tablosu -->
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Teklif No</th>
                <th>Firma</th>
                <th>Başlık</th>
                <th>Tarih</th>
                <th>Durum</th>
                <th>Toplam</th>
                <th>Net</th>
                <th>Numune İlerleme</th> <!-- ✅ kolon -->
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $offers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($offer->offer_no); ?></td>
                <td><?php echo e($offer->customer->name); ?></td>
                <td><?php echo e($offer->title); ?></td>
                <td><?php echo e($offer->offer_date); ?></td>
                <td>
                    <?php if($offer->status === 'approved'): ?>
                        <span class="badge bg-success">Onaylı</span>
                    <?php else: ?>
                        <span class="badge bg-secondary"><?php echo e(ucfirst($offer->status)); ?></span>
                    <?php endif; ?>
                </td>
                <td><?php echo e(number_format($offer->total_amount,2)); ?> ₺</td>
                <td><?php echo e(number_format($offer->net_amount,2)); ?> ₺</td>
                <td>
                    <?php
                        $used  = $offer->samples()->count();   // bağlanan numune
                        $total = $used + $offer->sample_count; // başlangıçtaki toplam hak
                    ?>
                    <span class="badge bg-info"><?php echo e($used); ?>/<?php echo e($total); ?></span>
                </td>
                <td>
                    <a href="<?php echo e(route('offers.show',$offer->id)); ?>" class="btn btn-info btn-sm">Detay</a>
                    <a href="<?php echo e(route('offers.edit',$offer->id)); ?>" class="btn btn-warning btn-sm">Güncelle</a>
                    <form action="<?php echo e(route('offers.destroy',$offer->id)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Silmek istediğine emin misin?')">Sil</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="9" class="text-center">Henüz teklif bulunmamaktadır.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/offers/index.blade.php ENDPATH**/ ?>