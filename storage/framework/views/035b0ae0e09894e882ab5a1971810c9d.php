

<?php $__env->startSection('title', 'Yeni İş Emri Oluştur'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
  <div class="col-md-8 offset-md-2">
    <h4 class="mb-4">Yeni İş Emri</h4>

    <form action="<?php echo e(route('production_orders.store')); ?>" method="POST">
      <?php echo csrf_field(); ?>

      <!-- Ürün -->
      <div class="mb-3">
        <label for="product_id" class="form-label">Ürün</label>
        <select name="product_id" id="product_id" class="form-select" required>
          <option value="">Seçiniz</option>
          <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($product->id); ?>" <?php echo e(old('product_id') == $product->id ? 'selected' : ''); ?>>
              <?php echo e($product->name); ?>

            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>

      <!-- Üretim Hattı -->
      <div class="mb-3">
        <label for="production_line_id" class="form-label">Üretim Hattı</label>
        <select name="production_line_id" id="production_line_id" class="form-select" required>
          <option value="">Seçiniz</option>
          <?php $__currentLoopData = $lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($line->id); ?>" <?php echo e(old('production_line_id') == $line->id ? 'selected' : ''); ?>>
              <?php echo e($line->name); ?>

            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>

      <!-- Formül -->
      <div class="mb-3">
        <label for="formula_id" class="form-label">Formül</label>
        <select name="formula_id" id="formula_id" class="form-select" required>
          <option value="">Seçiniz</option>
          <?php $__currentLoopData = $formulas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $formula): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($formula->id); ?>" <?php echo e(old('formula_id') == $formula->id ? 'selected' : ''); ?>>
              <?php echo e($formula->name); ?>

            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>

      <!-- Müşteri -->
      <div class="mb-3">
        <label for="customer_id" class="form-label">Müşteri</label>
        <select name="customer_id" id="customer_id" class="form-select" required>
          <option value="">Seçiniz</option>
          <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($customer->id); ?>" <?php echo e(old('customer_id') == $customer->id ? 'selected' : ''); ?>>
              <?php echo e($customer->name); ?>

            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>

      <!-- Planlanan Başlangıç Tarihi -->
      <div class="mb-3">
        <label for="planned_at" class="form-label">Planlanan Başlangıç Tarihi</label>
        <input type="datetime-local" name="planned_at" id="planned_at" class="form-control"
               value="<?php echo e(old('planned_at')); ?>" required>
      </div>

      <!-- Plan Bitiş Tarihi -->
      <div class="mb-3">
        <label for="planned_end_at" class="form-label">Plan Bitiş Tarihi</label>
        <input type="datetime-local" name="planned_end_at" id="planned_end_at" class="form-control"
               value="<?php echo e(old('planned_end_at')); ?>">
      </div>

      <!-- Miktar -->
      <div class="mb-3">
        <label for="quantity_kg" class="form-label">Miktar (kg)</label>
        <input type="number" name="quantity_kg" id="quantity_kg" class="form-control"
               value="<?php echo e(old('quantity_kg')); ?>" step="0.01" min="0.01" required>
      </div>

      <!-- Notlar -->
      <div class="mb-3">
        <label for="notes" class="form-label">Notlar</label>
        <textarea name="notes" id="notes" class="form-control" rows="3"><?php echo e(old('notes')); ?></textarea>
      </div>

      <button type="submit" class="btn btn-primary">İş Emrini Oluştur</button>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/production_orders/create.blade.php ENDPATH**/ ?>