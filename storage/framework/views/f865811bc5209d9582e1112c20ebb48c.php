

<?php $__env->startSection('title','Toplu Mail Gönderimi'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>📧 Toplu Mail Gönderimi</h4>
<?php if(session('success')): ?>
    <div class="alert alert-success">
        <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="alert alert-danger">
        <?php echo e(session('error')); ?>

    </div>
<?php endif; ?>
    <!-- Ürün + Tarih Aralığı -->
    <form method="GET" action="<?php echo e(route('samples.toplumail')); ?>" class="row mb-3">
        <div class="col-md-4">
            <label for="product_id">Ürün Seç</label>
            <select id="product_id" name="product_id" class="form-control">
                <option value="">Tümü</option>
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($product->id); ?>" <?php echo e(request('product_id') == $product->id ? 'selected' : ''); ?>>
                        <?php echo e($product->product_name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="start_date">Başlangıç Tarihi</label>
            <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo e(request('start_date')); ?>">
        </div>
        <div class="col-md-3">
            <label for="end_date">Bitiş Tarihi</label>
            <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo e(request('end_date')); ?>">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-search"></i> Ara
            </button>
        </div>
    </form>

    <!-- ✅ Rapor Listesi -->
    <?php if($samples->count()): ?>
    <form method="POST" action="<?php echo e(route('samples.sendBulk')); ?>">
        <?php echo csrf_field(); ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all"> Tümünü Seç</th>
                    <th>Firma</th>
                    <th>Ürün</th>
                    <th>Rapor No</th>
                    <th>Rapor Tarihi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $samples; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sample): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><input type="checkbox" name="sample_ids[]" value="<?php echo e($sample->id); ?>"></td>
                    <td><?php echo e($sample->company_name); ?></td>
                    <td><?php echo e($sample->labProduct->product_name); ?></td>
                    <td><?php echo e($sample->report_no); ?></td>
                    <td><?php echo e($sample->report_date?->format('d.m.Y')); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <!-- Ekstra Mail Adresleri -->
        <label for="emails">Ekstra Mail Adresleri</label>
        <textarea id="emails" name="emails" class="form-control mb-2" placeholder="Mail adreslerini virgülle ayırın (boş bırakabilirsiniz)"></textarea>

        <!-- Gönderim seçenekleri -->
        <button type="submit" name="mode" value="selected" class="btn btn-success">
            <i class="fas fa-envelope"></i> Seçilenleri Gönder
        </button>
        <button type="submit" name="mode" value="all" class="btn btn-warning">
            <i class="fas fa-envelope"></i> Tümünü Gönder
        </button>
    </form>
    <?php else: ?>
        <div class="alert alert-info">Seçilen ürün ve tarihlerde rapor bulunamadı.</div>
    <?php endif; ?>

    <!-- Sabit Gönderilen Mailler -->
    <h5 class="mt-4">📌 Sabit Gönderilen Mailler</h5>
    <ul>
        <?php $__currentLoopData = $defaultEmails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($mail); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</div>

<script>
document.getElementById('select-all')?.addEventListener('click', function(e) {
    const checkboxes = document.querySelectorAll('input[name="sample_ids[]"]');
    checkboxes.forEach(cb => cb.checked = e.target.checked);
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/sample_acceptances/toplumail.blade.php ENDPATH**/ ?>