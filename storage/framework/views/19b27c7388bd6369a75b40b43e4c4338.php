
<?php $__env->startSection('title', 'Fiziksel Parametreler'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
  <h4 class="mb-4">Tanımlı Fiziksel Parametreler</h4>

  <a href="<?php echo e(route('physical_parameter.create')); ?>" class="btn btn-primary mb-3">Yeni Parametre Ekle</a>

  <?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?>

  <table class="table table-bordered table-striped">
    <thead class="table-light">
      <tr>
        <th style="width: 60px;">#</th>
        <th>Ad</th>
        <th>Grup</th>
        <th>Açıklama</th>
        <th style="width: 160px;">İşlem</th>
      </tr>
    </thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $parameters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parameter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td><?php echo e($loop->iteration); ?></td>
          <td><?php echo e($parameter->name); ?></td>
          <td><?php echo e($parameter->group); ?></td>
          <td><?php echo e($parameter->description); ?></td>
          <td>
            <a href="<?php echo e(route('physical_parameter.edit', $parameter->id)); ?>" class="btn btn-sm btn-warning">Düzenle</a>
            <form action="<?php echo e(route('physical_parameter.destroy', $parameter->id)); ?>" method="POST" class="d-inline">
              <?php echo csrf_field(); ?>
              <?php echo method_field('DELETE'); ?>
              <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Silmek istediğine emin misin?')">Sil</button>
            </form>
          </td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
          <td colspan="5" class="text-center text-muted">Henüz fiziksel parametre tanımlanmadı.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/physical_parameter/index.blade.php ENDPATH**/ ?>