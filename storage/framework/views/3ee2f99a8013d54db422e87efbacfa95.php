

<?php $__env->startSection('title', 'Ham Madde Düzenle'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">Ham Maddeyi Düzenle</h5>
            </div>
            <div class="card-body">
                <?php if($errors->any()): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?php echo e(route('raw-materials.update', $rawMaterial->id)); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

                    <div class="form-group">
                        <label for="name">Ad</label>
                        <input type="text" name="name" id="name" class="form-control"
                               value="<?php echo e(old('name', $rawMaterial->name)); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="unit">Birim</label>
                        <input type="text" name="unit" id="unit" class="form-control"
                               value="<?php echo e(old('unit', $rawMaterial->unit)); ?>">
                    </div>

                    <div class="form-group">
                        <label for="stock_kg">Stok (kg)</label>
                        <input type="number" step="0.01" name="stock_kg" id="stock_kg" class="form-control"
                               value="<?php echo e(old('stock_kg', $rawMaterial->stock_kg)); ?>">
                    </div>

                    <hr>
                    <h5>Fiyat Bilgileri</h5>

                    <div class="form-group">
                        <label for="unit_price_tl">Fiyat (TL)</label>
                        <input type="number" step="0.01" name="unit_price_tl" id="unit_price_tl" class="form-control"
                               value="<?php echo e(old('unit_price_tl', $rawMaterial->unit_price_tl)); ?>">
                    </div>

                    <div class="form-group">
                        <label for="unit_price_usd">Fiyat (USD)</label>
                        <input type="number" step="0.01" name="unit_price_usd" id="unit_price_usd" class="form-control"
                               value="<?php echo e(old('unit_price_usd', $rawMaterial->unit_price_usd)); ?>">
                    </div>

                    <div class="form-group">
                        <label for="unit_price_eur">Fiyat (EUR)</label>
                        <input type="number" step="0.01" name="unit_price_eur" id="unit_price_eur" class="form-control"
                               value="<?php echo e(old('unit_price_eur', $rawMaterial->unit_price_eur)); ?>">
                    </div>

                    <div class="form-group">
                        <label for="currency">Aktif Para Birimi</label>
                        <select name="currency" id="currency" class="form-control">
                            <option value="TL" <?php echo e(old('currency', $rawMaterial->currency) == 'TL' ? 'selected' : ''); ?>>TL</option>
                            <option value="USD" <?php echo e(old('currency', $rawMaterial->currency) == 'USD' ? 'selected' : ''); ?>>USD</option>
                            <option value="EUR" <?php echo e(old('currency', $rawMaterial->currency) == 'EUR' ? 'selected' : ''); ?>>EUR</option>
                        </select>
                    </div>

                    <hr>
                    <h5>Yarı Mamul Bilgileri</h5>

                    <div class="form-group">
                        <label for="semi_doc">Katkı Dokümanı (PDF)</label>
                        <?php if($rawMaterial->semi_doc): ?>
                            <p>Mevcut: <a href="<?php echo e(asset('storage/'.$rawMaterial->semi_doc)); ?>" target="_blank" class="btn btn-sm btn-info">PDF Görüntüle</a></p>
                        <?php endif; ?>
                        <input type="file" name="semi_doc" id="semi_doc" class="form-control" accept="application/pdf">
                    </div>

                    <div class="form-group">
                        <label for="usage_rate">Kullanım Oranı (%)</label>
                        <input type="number" step="0.01" name="usage_rate" id="usage_rate" class="form-control"
                               value="<?php echo e(old('usage_rate', $rawMaterial->usage_rate)); ?>">
                    </div>

                    <div class="form-group">
                        <label for="performance_level">Performans Seviyesi</label>
                        <textarea name="performance_level" id="performance_level" class="form-control"><?php echo e(old('performance_level', $rawMaterial->performance_level)); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="competitor_product">Rakip Ürün</label>
                        <input type="text" name="competitor_product" id="competitor_product" class="form-control"
                               value="<?php echo e(old('competitor_product', $rawMaterial->competitor_product)); ?>">
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?php echo e(route('raw-materials.index')); ?>" class="btn btn-secondary">Geri</a>
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/rawmaterials/edit.blade.php ENDPATH**/ ?>