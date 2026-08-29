

<?php $__env->startSection('title', 'Ürünler'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4>📦 Ürün Listesi</h4>
    <div class="d-flex gap-2">
      <a href="<?php echo e(route('sales_products.create')); ?>" class="btn btn-success">
        <i class="fas fa-plus"></i> Manuel Ürün Ekle
      </a>
      <form action="<?php echo e(route('sales_products.import')); ?>" method="POST" enctype="multipart/form-data" class="d-inline">
        <?php echo csrf_field(); ?>
        <div class="input-group">
          <input type="file" name="file" class="form-control form-control-sm" required>
          <button type="submit" class="btn btn-info btn-sm">
            <i class="fas fa-file-excel"></i> Excel Yükle/Güncelle
          </button>
        </div>
      </form>
    </div>
  </div>

  <div class="alert alert-warning mb-4">
    <strong>Not:</strong> Excel dosyasında <code>id</code> dolu olan satırlar mevcut ürünleri günceller,
    <code>id</code> boş bırakılan satırlar ise yeni ürün olarak eklenir.
  </div>

  <?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?>

  <?php if($products->isEmpty()): ?>
    <div class="alert alert-info">Sonuç bulunamadı.</div>
  <?php else: ?>
    <div class="card card-outline card-primary">
      <div class="card-body p-0">
        <table class="table table-bordered table-striped align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Ürün Adı</th>
              <th>Performans Sınıfı</th>
              <th>Hacim</th>
              <th>Fiyat (USD)</th>
              <th>Fiyat (TL)</th>
              <th class="text-center">İşlem</th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr>
                <td><?php echo e($loop->iteration); ?></td>
                <td><?php echo e($product->name); ?></td>
                <td><?php echo e($product->performance_class ?? '-'); ?></td>
                <td><?php echo e($product->volume ?? '-'); ?></td>
                <td><?php echo e(number_format($product->price_usd, 2)); ?> $</td>
                <td><?php echo e(number_format($product->price_try, 2)); ?> ₺</td>
                <td class="text-center">
                  <div class="d-flex justify-content-center gap-2">
                    <a href="<?php echo e(route('sales_products.edit', $product->id)); ?>" class="btn btn-sm btn-outline-primary">
                      <i class="fas fa-edit"></i> Düzenle
                    </a>
                    <form action="<?php echo e(route('sales_products.destroy', $product->id)); ?>" method="POST"
                          onsubmit="return confirm('Bu ürünü silmek istediğinize emin misiniz?')" class="d-inline">
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
        Toplam Ürün: <?php echo e($products->count()); ?>

      </div>
    </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/sales_products/index.blade.php ENDPATH**/ ?>