

<?php $__env->startSection('title', 'Kalite Kontrol Kayıtları'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
  <h4>🧪 Kalite Kontrol Kayıtları</h4>
  <a href="<?php echo e(route('quality-checks.create')); ?>" class="btn btn-primary">
    + Yeni Kayıt Ekle
  </a>
</div>


  <table class="table table-bordered table-sm">
    <thead class="table-light">
      <tr>
        <th>#</th>
        <th>Ürün</th>
        <th>Kategori</th>
        <th>Miktar</th>
        <th>Birim</th>
        <th>Tedarikçi</th>
        <th>İrsaliye No</th>
        <th>Geliş Tarihi</th>
        <th>Durum</th>
        <th>Kontrol Eden</th>
        <th>Not</th>
      </tr>
    </thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $checks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $check): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td><?php echo e($check->id); ?></td>
          <td><?php echo e($check->rawMaterial->name ?? '-'); ?></td>
          <td><?php echo e(ucfirst($check->kategori)); ?></td>
          <td><?php echo e($check->amount); ?></td>
          <td><?php echo e($check->unit); ?></td>
          <td><?php echo e($check->supplier_name ?? '-'); ?></td>
          <td><?php echo e($check->delivery_note_number ?? '-'); ?></td>
          <td><?php echo e(\Carbon\Carbon::parse($check->received_at)->format('d.m.Y')); ?></td>
          <td>
            <span class="badge bg-<?php echo e($check->status == 'onaylandı' ? 'success' : ($check->status == 'reddedildi' ? 'danger' : 'secondary')); ?>">
              <?php echo e(ucfirst($check->status)); ?>

            </span>
          </td>
          <td><?php echo e($check->user->name ?? '-'); ?></td>
          <td><?php echo e($check->notes); ?></td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="11">Kayıt bulunamadı.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="mt-3">
    <?php echo e($checks->links()); ?>

  </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/quality_checks/index.blade.php ENDPATH**/ ?>