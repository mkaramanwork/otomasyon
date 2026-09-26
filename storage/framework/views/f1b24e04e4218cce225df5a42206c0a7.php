
<?php $__env->startSection('title','Teklif Güncelle'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4 class="mb-3">✏️ Teklif Güncelle - <?php echo e($offer->offer_no); ?></h4>

    <form action="<?php echo e(route('offers.update',$offer->id)); ?>" method="POST">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

        <!-- Firma -->
        <div class="mb-3">
            <label class="form-label">Firma</label>
            <select name="customer_id" class="form-control" required>
                <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($customer->id); ?>" <?php if($offer->customer_id==$customer->id): ?> selected <?php endif; ?>>
                        <?php echo e($customer->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <!-- Başlık -->
        <div class="mb-3">
            <label class="form-label">Başlık</label>
            <input type="text" name="title" class="form-control" value="<?php echo e($offer->title); ?>">
        </div>

        <!-- Teklif Metni -->
        <div class="mb-3">
            <label class="form-label">Teklif Metni</label>
            <textarea name="text" class="form-control" rows="4"><?php echo e($offer->text); ?></textarea>
        </div>

        <!-- Teklif Tarihi -->
        <div class="mb-3">
            <label class="form-label">Teklif Tarihi</label>
            <input type="date" name="offer_date" class="form-control" value="<?php echo e($offer->offer_date); ?>">
        </div>

        <!-- Numune Adedi -->
        <div class="mb-3">
            <label class="form-label">Numune Adedi</label>
            <input type="number" name="sample_count" class="form-control" value="<?php echo e($offer->sample_count); ?>">
        </div>

        <!-- Parametreler -->
        <h5 class="mt-4">Parametreler</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Seç</th>
                    <th>Parametre</th>
                    <th>Metot</th>
                    <th>Fiyat (₺)</th>
                    <th>Kapsam</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $parameters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $param): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    // OfferParameter tablosundaki kayıtları kontrol et
                    $existing = $offer->parameters->firstWhere('parameter_id',$param->id);
                ?>
                <tr>
                    <td>
                        <input type="checkbox" name="parameters[<?php echo e($param->id); ?>][selected]" value="1"
                               <?php if($existing): ?> checked <?php endif; ?>>
                        <input type="hidden" name="parameters[<?php echo e($param->id); ?>][id]" value="<?php echo e($param->id); ?>">
                    </td>
                    <td><?php echo e($param->parameter); ?></td>
                    <td>
                        <input type="text" name="parameters[<?php echo e($param->id); ?>][method]" class="form-control"
                               value="<?php echo e($existing->method ?? $param->method_tr); ?>">
                    </td>
                    <td>
                        <input type="number" step="0.01" name="parameters[<?php echo e($param->id); ?>][price]" class="form-control"
                               value="<?php echo e($existing->price ?? ''); ?>">
                    </td>
                    <td>
                        <input type="text" name="parameters[<?php echo e($param->id); ?>][scope]" class="form-control"
                               value="<?php echo e($existing->scope ?? ''); ?>">
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <!-- İndirim Oranı -->
        <div class="mb-3">
            <label class="form-label">İndirim Oranı (%)</label>
            <input type="number" step="0.01" name="discount_rate" class="form-control" value="<?php echo e($offer->discount_rate); ?>">
        </div>

        <!-- Kaydet -->
        <button type="submit" class="btn btn-primary">💾 Güncelle</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/offers/edit.blade.php ENDPATH**/ ?>