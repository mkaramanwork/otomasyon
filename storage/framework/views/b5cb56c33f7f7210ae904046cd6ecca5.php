

<?php $__env->startSection('title','Numune Sonuç Girişi'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>📄 Numune Sonuç Girişi</h4>

    <!-- ✅ Numune bilgileri -->
    <div class="mb-3">
        <strong>Batch No:</strong> <?php echo e($sample->batch_no); ?> <br>
        <strong>Müşteri:</strong> <?php echo e($sample->company_name); ?> <br>
        <strong>Ürün Adı:</strong> <?php echo e($sample->labProduct->product_name ?? '-'); ?> <br>
        <strong>Rapor No:</strong> <?php echo e($sample->report_no ?? '-'); ?>

    </div>

    <!-- ✅ Parametreler tablosu -->
    <?php if($sample->parameters->isEmpty()): ?>
        <div class="alert alert-warning">Bu numuneye ait parametre bulunamadı.</div>
    <?php else: ?>
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Parametre</th>
                    <th>Metot (TR)</th>
                    <th>Ölçüm Belirsizliği</th>
                    <th>Sonuç</th>
                    <th>Tarih</th>
                    <th>Açıklama</th>
                    <th>Durum</th>
                    <th>Kaydet</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $sample->parameters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $param): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <form action="<?php echo e(route('samples.results.storeSingle', [$sample->id, $param->id])); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <td><?php echo e($param->parameter); ?></td>
                            <td><?php echo e($param->method_tr); ?></td>
                            <td><?php echo e($param->uncertainty); ?></td>
                            <td>
                                <input type="text" class="form-control"
                                       name="value" value="<?php echo e($param->pivot->result); ?>">
                            </td>
                            <td>
                                <input type="datetime-local" class="form-control"
                                       name="date"
                                       value="<?php echo e($param->pivot->result_date ?? now()->format('Y-m-d\TH:i')); ?>">
                            </td>
                            <td>
                                <input type="text" class="form-control"
                                       name="description" value="<?php echo e($param->pivot->description); ?>">
                            </td>
                            <td>
                                <?php echo e($param->parameter); ?>

                                <?php if($param->pivot->result): ?>
                                    <span class="badge bg-success">✔ Kaydedildi</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Bekliyor</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="submit" class="btn btn-success btn-sm">Kaydet</button>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <?php if($sample->logs->isNotEmpty()): ?>
            <tr>
                <td colspan="7">
                    <strong>📜 İşlem Geçmişi:</strong>
                    <table class="table table-sm table-bordered mt-2">
                        <thead>
                            <tr>
                                <th>Tarih</th>
                                <th>Kullanıcı</th>
                                <th>İşlem</th>
                                <th>Açıklama</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $sample->logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($log->created_at->format('d.m.Y H:i')); ?></td>
                                    <td><?php echo e($log->user->name ?? 'Sistem'); ?></td>
                                    <td><?php echo e($log->action); ?></td>
                                    <td><?php echo e($log->description); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </td>
            </tr>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/sample_acceptances/results_edit.blade.php ENDPATH**/ ?>