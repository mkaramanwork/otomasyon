

<?php $__env->startSection('title', 'Parametreler'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4>🧪 Parametre Listesi</h4>
    <div class="d-flex gap-2">
      <a href="<?php echo e(route('parameters.create')); ?>" class="btn btn-success">
        <i class="fas fa-plus"></i> Yeni Parametre Ekle
      </a>
    </div>
  </div>

  <?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?>

  <?php if($parameters->isEmpty()): ?>
    <div class="alert alert-info">Sonuç bulunamadı.</div>
  <?php else: ?>
    <div class="card card-outline card-primary">
      <div class="card-body p-0">
        <table class="table table-bordered table-striped align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Numune Türü</th>
              <th>Parametre</th>
              <th>Metod (TR)</th>
              <th>Metod (EN)</th>
              <th>Metod (RU)</th>
              <th>Birim</th>
              <th>Kapsam</th>
              <th>Belirsizlik</th> <!-- ✅ yeni sütun -->
              <th class="text-center">İşlem</th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $parameters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parameter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr>
                <td><?php echo e($loop->iteration); ?></td>
                <td><?php echo e($parameter->sample_type); ?></td>
                <td><?php echo e($parameter->parameter); ?></td>
                <td><?php echo e($parameter->method_tr); ?></td>
                <td><?php echo e($parameter->method_en); ?></td>
                <td><?php echo e($parameter->method_ru); ?></td>
                <td><?php echo e($parameter->unit); ?></td>
                <td><?php echo e($parameter->scope); ?></td>
                <td><?php echo e($parameter->uncertainty ?? '-'); ?></td> <!-- ✅ belirsizlik değeri -->
                <td class="text-center">
                  <div class="d-flex justify-content-center gap-2">
                    <a href="<?php echo e(route('parameters.edit', $parameter->id)); ?>" class="btn btn-sm btn-outline-primary">
                      <i class="fas fa-edit"></i> Düzenle
                    </a>
                    <form action="<?php echo e(route('parameters.destroy', $parameter->id)); ?>" method="POST"
                          onsubmit="return confirm('Bu parametreyi silmek istediğinize emin misiniz?')" class="d-inline">
                      <?php echo csrf_field(); ?>
                      <?php echo method_field('DELETE'); ?>
                      <button type="submit" class="btn btn-sm btn-outline-danger">
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
        Toplam Parametre: <?php echo e($parameters->count()); ?>

      </div>
    </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/parameters/index.blade.php ENDPATH**/ ?>