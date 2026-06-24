
<?php $__env->startSection('title', 'Fiziksel Reçeteler'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
  <h4 class="mb-4">Tanımlı Fiziksel Reçeteler</h4>

  <a href="<?php echo e(route('physcial_formula.create')); ?>" class="btn btn-primary mb-3">Yeni Reçete Oluştur</a>

  <?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?>

  <table class="table table-bordered table-striped">
    <thead class="table-light">
      <tr>
        <th style="width: 60px;">#</th>
        <th>Ürün Kodu</th>
        <th>Ürün Adı</th>
        <th>Birim</th>
        <th>Açıklama</th>
        <th style="width: 240px;">İşlemler</th>
      </tr>
    </thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $formulas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $formula): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td><?php echo e($loop->iteration); ?></td>
          <td><?php echo e($formula->product_code); ?></td>
          <td><?php echo e($formula->product_name); ?></td>
          <td><?php echo e($formula->unit); ?></td>
          <td><?php echo e($formula->description); ?></td>
          <td>
            <a href="<?php echo e(route('physcial_formula.edit', ['physcial_formula' => $formula->id])); ?>" class="btn btn-sm btn-warning">Düzenle</a>
            <a href="<?php echo e(route('physcial_formula.details', $formula->id)); ?>" class="btn btn-sm btn-info">Parametreler</a>
            <a href="<?php echo e(route('physcial_formula.addDetail', $formula->id)); ?>" class="btn btn-sm btn-success">Parametre Ekle</a>
            <form action="<?php echo e(route('physcial_formula.destroy', ['physcial_formula' => $formula->id])); ?>" method="POST" class="d-inline">
              <?php echo csrf_field(); ?>
              <?php echo method_field('DELETE'); ?>
              <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Silmek istediğine emin misin?')">Sil</button>
            </form>
          </td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
          <td colspan="6" class="text-center text-muted">Henüz fiziksel reçete tanımlanmadı.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/physcial_formula/index.blade.php ENDPATH**/ ?>