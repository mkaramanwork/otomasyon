

<?php $__env->startSection('title','Laboratuvar Raporlama'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>📑 Laboratuvar Raporlama</h4>

    <!-- ✅ Analizi Tamamlanan Numuneler -->
    <h5>✔ Analizi Tamamlanan Numuneler</h5>

    <!-- Arama kutusu -->
    <input type="text" id="sampleSearch" class="form-control mb-2" placeholder="Firma, Ürün Adı veya Batch No ile ara...">

    <table class="table table-bordered table-striped" id="samplesTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Firma</th>
                <th>Ürün Adı</th>
                <th>Batch No</th>
                <th>Rapor No</th>
                <th>Rapor Tarihi</th>
                <th>Durum</th>
                <th class="text-center">İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $samples; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sample): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $total   = $sample->parameters->count();
                    $done    = $sample->parameters->whereNotNull('pivot.result')->count();
                    $percent = $total > 0 ? round(($done / $total) * 100) : 0;
                ?>
                <?php if($percent == 100): ?>
                <tr>
                    <td><?php echo e($loop->iteration); ?></td>
                    <td><?php echo e($sample->company_name); ?></td>
                    <td><?php echo e($sample->labProduct->product_name ?? '-'); ?></td>
                    <td><?php echo e($sample->batch_no); ?></td>
                    <td><?php echo e($sample->report_no ?? '-'); ?></td>
                    <td><?php echo e($sample->report_date ? $sample->report_date->format('d.m.Y') : '-'); ?></td>
                    <td><span class="badge bg-success">Tamamlandı</span></td>
                    <td class="text-center">
                        <!-- Görüntüle -->
                        <a href="<?php echo e(route('samples.show',$sample->id)); ?>" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> Görüntüle
                        </a>

                        <!-- Rapor Yaz Butonları -->
                        <?php if($sample->report_date): ?>
                            <!-- Word -->
                            <a href="<?php echo e(route('samples.report.generate',['sample'=>$sample->id,'lang'=>'TR'])); ?>" class="btn btn-sm btn-primary">TR</a>
                            <a href="<?php echo e(route('samples.report.generate',['sample'=>$sample->id,'lang'=>'EN'])); ?>" class="btn btn-sm btn-secondary">EN</a>
                            <a href="<?php echo e(route('samples.report.generate',['sample'=>$sample->id,'lang'=>'US'])); ?>" class="btn btn-sm btn-dark">US</a>

                            <!-- Mail Gönder -->
                            <form action="<?php echo e(route('samples.sendMail',$sample->id)); ?>" method="POST" style="display:inline-block;">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="fas fa-envelope"></i> Mail Gönder
                                </button>
                            </form>
                        <?php else: ?>
                            <span class="badge bg-warning">Önce rapor kaydedilmeli</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" class="text-center">Henüz tamamlanan numune yok.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- ✅ Toast Bildirimi -->
<?php if(session('success')): ?>
    <div class="alert alert-success mt-3">
        <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>

<!-- JS: Firma, Ürün Adı ve Batch No arama -->
<script>
document.getElementById('sampleSearch').addEventListener('keyup', function() {
    let searchValue = this.value.toLowerCase();
    let rows = document.querySelectorAll('#samplesTable tbody tr');
    rows.forEach(row => {
        let firma   = row.cells[1]?.innerText.toLowerCase();
        let urun    = row.cells[2]?.innerText.toLowerCase();
        let batch   = row.cells[3]?.innerText.toLowerCase();
        if (firma.includes(searchValue) || urun.includes(searchValue) || batch.includes(searchValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/sample_acceptances/report.blade.php ENDPATH**/ ?>