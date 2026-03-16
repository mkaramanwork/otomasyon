

<?php $__env->startSection('title', 'Uygunsuzluklar'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4>⚠️ Uygunsuzluk Listesi</h4>
    <a href="<?php echo e(route('uygunsuzluk.create')); ?>" class="btn btn-success">Yeni Uygunsuzluk Ekle</a>
  </div>

  <?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?>

  <?php if($records->isEmpty()): ?>
    <div class="alert alert-info">Sonuç bulunamadı.</div>
  <?php else: ?>
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Bölüm</th>
          <th>Tanım</th>
          <th>Bildirim Tarihi</th>
          <th>Durum</th>
          <th class="text-center">İşlem</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td><?php echo e($loop->iteration); ?></td>
            <td><?php echo e($rec->bolum); ?></td>
            <td><?php echo e($rec->tanim); ?></td>
            <td><?php echo e($rec->bildirim_tarihi); ?></td>
            <td>
              <span class="badge bg-<?php echo e($rec->durum === 'closed' ? 'success' : 'warning'); ?>">
                <?php echo e(ucfirst($rec->durum)); ?>

              </span>
            </td>
            <td class="text-center">
              <div class="d-flex justify-content-center gap-2">
                <a href="<?php echo e(route('uygunsuzluk.show', $rec->id)); ?>" class="btn btn-sm btn-outline-info">Detay</a>
                <a href="<?php echo e(route('uygunsuzluk.edit', $rec->id)); ?>" class="btn btn-sm btn-outline-primary">Düzenle</a>
                <form action="<?php echo e(route('uygunsuzluk.destroy', $rec->id)); ?>" method="POST" 
                      onsubmit="return confirm('Bu kaydı silmek istediğinize emin misiniz?')" style="display:inline;">
                  <?php echo csrf_field(); ?>
                  <?php echo method_field('DELETE'); ?>
                  <button type="submit" class="btn btn-sm btn-outline-danger">Sil</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/uygunsuzluk/index.blade.php ENDPATH**/ ?>