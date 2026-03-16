
<?php $__env->startSection('title','Analiz Bekleyen Numuneler'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>📊 Raporlama - Analiz Bekleyen Numuneler</h4>

    <!-- ✅ Özet Kartlar -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title">Toplam Numune</h6>
                    <h4><?php echo e($samples->count()); ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="card-title">Tamamlanan Numune</h6>
                    <h4>
                        <?php echo e($samples->filter(function($s){
                            $total = $s->parameters->count();
                            $done = $s->parameters->whereNotNull('pivot.result')->count();
                            return $total > 0 && $done == $total;
                        })->count()); ?>

                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Arama / Filtreleme Formu -->
    <form method="GET" action="<?php echo e(route('samples.analysis-pending')); ?>" class="row g-3 mb-4">
        <div class="col-md-2">
            <input type="text" name="batch_no" class="form-control" placeholder="Batch No" value="<?php echo e(request('batch_no')); ?>">
        </div>
        <div class="col-md-3">
            <input type="text" name="company_name" class="form-control" placeholder="Firma" value="<?php echo e(request('company_name')); ?>">
        </div>
        <div class="col-md-2">
            <input type="text" name="report_no" class="form-control" placeholder="Rapor No" value="<?php echo e(request('report_no')); ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Ara</button>
        </div>
    </form>

    <?php if($samples->isEmpty()): ?>
        <div class="alert alert-info">Şu anda analiz bekleyen numune yok.</div>
    <?php else: ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Batch No</th>
                    <th>Firma</th>
                    <th>Rapor No</th>
                    <th>Numune Adı</th>
                    <th>Kabul Tarihi</th>
                    <th>İlerleme Durumu</th>
                    <th class="text-center">İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $samples; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sample): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $total = $sample->parameters->count();
                        $done = $sample->parameters->whereNotNull('pivot.result')->count();
                        $percent = $total > 0 ? round(($done / $total) * 100) : 0;
                    ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td><?php echo e($sample->batch_no); ?></td>
                        <td><?php echo e($sample->company_name); ?></td>
                        <td><?php echo e($sample->report_no ?? '-'); ?></td>
                        <td><?php echo e($sample->product->name ?? '-'); ?></td>
                        <td><?php echo e($sample->taken_at ? $sample->taken_at->format('d.m.Y H:i') : '-'); ?></td>
                        <td>
                            <div class="progress" style="height:20px;">
                                <div class="progress-bar 
                                    <?php if($percent < 50): ?> bg-danger 
                                    <?php elseif($percent < 100): ?> bg-warning 
                                    <?php else: ?> bg-success <?php endif; ?>"
                                    role="progressbar"
                                    style="width: <?php echo e($percent); ?>%;" aria-valuenow="<?php echo e($percent); ?>"
                                    aria-valuemin="0" aria-valuemax="100">
                                    <?php echo e($percent); ?>%
                                </div>
                            </div>
                            <?php if($percent == 100): ?>
                                <span class="badge bg-success mt-1">✔ Numune girişi tamamlandı</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a href="<?php echo e(route('samples.results.edit',$sample->id)); ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Sonuç Gir
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/sample_acceptances/analysis_pending.blade.php ENDPATH**/ ?>