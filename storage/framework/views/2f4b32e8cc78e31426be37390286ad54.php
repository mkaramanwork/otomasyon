

<?php $__env->startSection('title', 'Ürün Sicil Kartları'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4>📦 Ürün Sicil Kartları</h4>
  </div>

  <?php if(session('success')): ?>
    <div class="alert alert-success" id="successMessage">
      <?php echo e(session('success')); ?>

    </div>
    <script>
      setTimeout(function() {
        <?php if(session('new_product_id')): ?>
          window.location.href = "<?php echo e(route('urunAgaci.show', session('new_product_id'))); ?>";
        <?php endif; ?>
      }, 1000);
    </script>
  <?php endif; ?>

  <!-- Accordion: Ürün Ekleme Formu -->
  <div class="accordion mb-4" id="urunAccordion">
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingForm">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseForm">
          ➕ Yeni Ürün Ekle
        </button>
      </h2>
      <div id="collapseForm" class="accordion-collapse collapse" data-bs-parent="#urunAccordion">
        <div class="accordion-body">
          <form action="<?php echo e(route('products.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
              <label>Ürün Adı</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Ürün Kodu</label>
              <input type="text" name="code" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Birim</label>
              <select name="unit" class="form-control" required>
                <option value="kg">kg</option>
                <option value="lt">lt</option>
                <option value="adet">adet</option>
              </select>
            </div>
            <button type="submit" class="btn btn-primary">Kaydet</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Ürün Listesi -->
  <?php if($products->isEmpty()): ?>
    <div class="alert alert-info">Sonuç bulunamadı.</div>
  <?php else: ?>
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Ad</th>
          <th>Kod</th>
          <th>Birim</th>
          <th>Ekleyen</th>
          <th>Eklenme Tarihi</th>
          <th class="text-center">İşlem</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td><?php echo e($loop->iteration); ?></td>
            <td><?php echo e($product->name); ?></td>
            <td><?php echo e($product->code); ?></td>
            <td><?php echo e($product->unit); ?></td>
            <td><?php echo e($product->addedBy->name ?? '-'); ?></td>
            <td><?php echo e($product->created_at->format('d.m.Y H:i')); ?></td>
            <td class="text-center">
              <div class="d-flex justify-content-center gap-2">
                <a href="<?php echo e(route('urunAgaci.show', $product->id)); ?>" class="btn btn-sm btn-outline-primary">Detay</a>
                <a href="<?php echo e(route('products.edit', $product->id)); ?>" class="btn btn-sm btn-outline-secondary">Düzenle</a>
                <form action="<?php echo e(route('products.destroy', $product->id)); ?>" method="POST" onsubmit="return confirm('Bu ürünü silmek istediğinize emin misiniz?')" style="display:inline;">
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
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/products/index.blade.php ENDPATH**/ ?>