
<?php $__env->startSection('title', 'Yeni Fiziksel Reçete'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
  <h4 class="mb-4">Yeni Fiziksel Reçete Oluştur</h4>

  <form method="POST" action="<?php echo e(route('physcial_formula.store')); ?>">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="product_id" value="<?php echo e($productId); ?>">
    <div class="mb-3">
      <label class="form-label">Ürün Kodu</label>
      <input type="text" name="product_code" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Ürün Adı</label>
      <input type="text" name="product_name" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Birim</label>
      <input type="text" name="unit" class="form-control" placeholder="adet, kg, m²...">
    </div>

    <div class="mb-3">
      <label class="form-label">Açıklama</label>
      <textarea name="description" class="form-control" rows="3"></textarea>
    </div>

    <button type="submit" class="btn btn-success">Kaydet ve Parametre Ekle</button>
    <a href="<?php echo e(route('physcial_formula.index')); ?>" class="btn btn-secondary">Geri</a>
  </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/physcial_formula/create.blade.php ENDPATH**/ ?>