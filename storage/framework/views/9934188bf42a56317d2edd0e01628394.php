

<?php $__env->startSection('title','Ana Sayfa'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Başlık + Yeni Numune Kaydet butonu -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>🧪 Numune Kabul Listesi</h4>
        <a href="<?php echo e(route('samples.accept-manual-create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Numune Kaydet
        </a>
    </div>

    <!-- ✅ Kabul Edilen Numuneler -->
    <h5 class="mt-4">✅ Kabul Edilen Numuneler</h5>
    <?php if($acceptedSamples->isEmpty()): ?>
        <div class="alert alert-info">Henüz kabul edilen numune yok.</div>
    <?php else: ?>
        <div class="card card-outline card-success shadow-sm">
            <div class="card-body p-0">
                <table class="table table-bordered table-striped align-middle mb-0 sampleTable">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Ürün</th>
                            <th>Firma</th>
                            <th>Batch</th>
                            <th>Talep No</th>
                            <th>Numune Türü</th>
                            <th>Alındığı Yer</th>
                            <th>Alan Kişi</th>
                            <th>Alınma Tarihi</th>
                            <th class="text-center">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $acceptedSamples; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sample): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($loop->iteration); ?></td>
                                <td><?php echo e($sample->labProduct->product_name ?? '-'); ?></td>
                                <td><?php echo e($sample->company_name); ?></td>
                                <td><?php echo e($sample->batch_no); ?></td>
                                <td><?php echo e($sample->request_no); ?></td>
                                <td><?php echo e($sample->sample_type); ?></td>
                                <td><?php echo e($sample->taken_place); ?></td>
                                <td><?php echo e($sample->taken_by); ?></td>
                                <td><?php echo e($sample->taken_at); ?></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="<?php echo e(route('samples.labels',$sample->id)); ?>" target="_blank" class="btn btn-sm btn-success">
                                            <i class="fas fa-tag"></i> Etiket
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info"
                                                data-bs-toggle="modal"
                                                data-bs-target="#sampleModal<?php echo e($sample->id); ?>">
                                            <i class="fas fa-eye"></i> Detay
                                        </button>
                                        <a href="<?php echo e(route('samples.edit',$sample->id)); ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Güncelle
                                        </a>
                                        <form action="<?php echo e(route('samples.destroy',$sample->id)); ?>" method="POST" class="d-inline"
                                              onsubmit="return confirm('Bu numuneyi silmek istediğinize emin misiniz?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> Sil
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-muted">
                Toplam Kabul Edilen Numune: <?php echo e($acceptedSamples->count()); ?>

            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/pages/home.blade.php ENDPATH**/ ?>