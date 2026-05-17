

<?php $__env->startSection('title', 'Stok Hareketleri'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mt-4">

  <h4 class="mb-4">📦 Stok Hareketleri (Giriş & Çıkış)</h4>

  
  <form method="GET" class="row g-3 mb-4 p-3 border rounded bg-light">
    <div class="col-md-3">
      <label class="form-label fw-bold">Başlangıç Tarihi</label>
      <input type="date" name="start_date" value="<?php echo e($request->start_date); ?>" class="form-control">
    </div>

    <div class="col-md-3">
      <label class="form-label fw-bold">Bitiş Tarihi</label>
      <input type="date" name="end_date" value="<?php echo e($request->end_date); ?>" class="form-control">
    </div>

    <div class="col-md-4">
      <label class="form-label fw-bold">Ham Madde Adı</label>
      <input type="text" name="material" value="<?php echo e($request->material); ?>" class="form-control" placeholder="örn. MEG, Katık">
    </div>

    <div class="col-md-2 d-flex align-items-end gap-2">
      <button type="submit" class="btn btn-primary w-100">Filtrele</button>
      <a href="<?php echo e(route('reports.stoch')); ?>" class="btn btn-outline-secondary w-100">Temizle</a>
    </div>
  </form>

  
  <table class="table table-bordered table-striped table-hover align-middle">
    <thead class="table-dark">
      <tr>
        <th>#</th>
        <th>Ham Madde</th>
        <th>Tip</th>
        <th>Miktar</th>
        <th>Ürün / İş Emri</th>
        <th>Kullanıcı</th>
        <th>Tarih</th>
      </tr>
    </thead>

    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td><?php echo e($m->id); ?></td>

          
          <td class="fw-bold">
            <?php echo e($m->material->name ?? '-'); ?>

          </td>

          
          <td>
            <?php if($m->type === 'in'): ?>
              <span class="badge bg-success">⬆️ Ürün Girişi</span>
            <?php elseif($m->type === 'out' && $m->productionOrder): ?>
              <span class="badge bg-warning text-dark">🏭 Üretim Çıkışı</span>
            <?php elseif($m->type === 'out'): ?>
              <span class="badge bg-danger">⬇️ Stok Çıkışı</span>
            <?php else: ?>
              <span class="badge bg-secondary"><?php echo e(ucfirst($m->type)); ?></span>
            <?php endif; ?>
          </td>

          
          <td class="fw-bold"><?php echo e(number_format($m->amount, 2)); ?> kg</td>

          
          <td>
            <?php if($m->productionOrder): ?>
              <strong><?php echo e($m->productionOrder->product->name); ?></strong><br>
              <small class="text-muted">Batch: <?php echo e($m->productionOrder->batch_number); ?></small>
            <?php else: ?>
              <span class="text-muted">-</span>
            <?php endif; ?>
          </td>

          
          <td><?php echo e($m->triggeredBy->name ?? '-'); ?></td>

          
          <td><?php echo e($m->created_at->format('d.m.Y H:i')); ?></td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
          <td colspan="7" class="text-center text-muted py-3">
            Kayıt bulunamadı.
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

  
  <?php
    $totalIn = $movements->where('type', 'in')->sum('amount');
    $totalOut = $movements->where('type', 'out')->sum('amount');
  ?>

  <div class="row mt-4">
    <div class="col-md-6">
      <div class="p-3 bg-success text-white rounded shadow-sm">
        <h5 class="mb-1">Toplam Giriş</h5>
        <h3><?php echo e(number_format($totalIn, 2)); ?> kg</h3>
      </div>
    </div>

    <div class="col-md-6">
      <div class="p-3 bg-danger text-white rounded shadow-sm">
        <h5 class="mb-1">Toplam Çıkış</h5>
        <h3><?php echo e(number_format($totalOut, 2)); ?> kg</h3>
      </div>
    </div>
  </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/reports/stoch.blade.php ENDPATH**/ ?>