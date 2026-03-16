

<?php $__env->startSection('title','Şikayet Düzenleme'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mt-4">
    <h3>✍️ Şikayet Düzenleme: <?php echo e($complaint->complaint_no); ?></h3>

    <form method="POST" action="<?php echo e(route('complaints.update',$complaint->id)); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="mb-3">
            <label>Konu</label>
            <input type="text" name="subject" value="<?php echo e($complaint->subject); ?>" class="form-control">
        </div>

        <div class="mb-3">
            <label>Tanım</label>
            <textarea name="description" class="form-control" rows="4"><?php echo e($complaint->description); ?></textarea>
        </div>

        <div class="mb-3">
            <label>Sahibi</label>
            <input type="text" name="owner" value="<?php echo e($complaint->owner); ?>" class="form-control">
        </div>

        <div class="mb-3">
            <label>Telefon</label>
            <input type="text" name="phone" value="<?php echo e($complaint->phone); ?>" class="form-control">
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo e($complaint->email); ?>" class="form-control">
        </div>

        <div class="mb-3">
            <label>Şikayeti Alan</label>
            <input type="text" name="received_by" value="<?php echo e($complaint->received_by); ?>" class="form-control">
        </div>

        <div class="mb-3">
            <label>Ürün Seçimi</label>
            <select name="product_id" class="form-control">
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($p->id); ?>" <?php if($complaint->product_id==$p->id): ?> selected <?php endif; ?>><?php echo e($p->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Değerlendirme</label>
            <textarea name="evaluation" class="form-control" rows="4"><?php echo e($complaint->evaluation); ?></textarea>
        </div>

        <div class="mb-3">
            <label>Sonuç</label>
            <textarea name="result" class="form-control" rows="4"><?php echo e($complaint->result); ?></textarea>
        </div>

        <div class="mb-3">
            <label>DIF Açılacak mı?</label>
            <select name="dif_opened" class="form-control">
                <option value="hayir" <?php if($complaint->dif_opened=='hayir'): ?> selected <?php endif; ?>>Hayır</option>
                <option value="evet" <?php if($complaint->dif_opened=='evet'): ?> selected <?php endif; ?>>Evet</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Uygunsuzluk Açıklaması</label>
            <textarea name="dif_description" class="form-control" rows="3"><?php echo e($complaint->dif_description); ?></textarea>
        </div>

        <div class="mb-3">
            <label>Geri Bildirim</label>
            <textarea name="feedback" class="form-control" rows="3"><?php echo e($complaint->feedback); ?></textarea>
        </div>

        <div class="mb-3">
            <label>Yeni Dosya Ekle (Analiz Raporu / Görsel)</label>
            <input type="file" name="files[]" multiple class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Güncelle</button>
        <a href="<?php echo e(route('complaints.index')); ?>" class="btn btn-secondary">⬅️ Geri</a>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/complaints/edit.blade.php ENDPATH**/ ?>