

<?php $__env->startSection('title', 'Yeni Uygunsuzluk'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
  <h4 class="mb-4">➕ Yeni Uygunsuzluk Kaydı</h4>

  <?php if($errors->any()): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="<?php echo e(route('uygunsuzluk.store')); ?>" method="POST">
    <?php echo csrf_field(); ?>

    <div class="mb-3">
      <label class="form-label">Bölüm</label>
      <input type="text" name="bolum" class="form-control" value="<?php echo e(old('bolum')); ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Tanım</label>
      <input type="text" name="tanim" class="form-control" value="<?php echo e(old('tanim')); ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Bildirim Tarihi</label>
      <input type="date" name="bildirim_tarihi" class="form-control" value="<?php echo e(old('bildirim_tarihi')); ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Uygunsuzluk Sınıfı</label>
      <input type="text" name="uy_sinif" class="form-control" value="<?php echo e(old('uy_sinif')); ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Durum</label>
      <select name="durum" class="form-select">
        <option value="open">Open</option>
        <option value="in_progress">In Progress</option>
        <option value="closed">Closed</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Kaynak</label>
      <input type="text" name="kaynak" class="form-control" value="<?php echo e(old('kaynak')); ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Açıklama</label>
      <textarea name="uy_aciklama" class="form-control"><?php echo e(old('uy_aciklama')); ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Yapılacak Faaliyet</label>
      <input type="text" name="yapilacak_faaliyet" class="form-control" value="<?php echo e(old('yapilacak_faaliyet')); ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Kök Neden</label>
      <input type="text" name="kaplam_kok" class="form-control" value="<?php echo e(old('kaplam_kok')); ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Düzeltici Faaliyet</label>
      <input type="text" name="duzeltici_faaliyet" class="form-control" value="<?php echo e(old('duzeltici_faaliyet')); ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Tespit Eden</label>
      <input type="text" name="tespit_eden" class="form-control" value="<?php echo e(old('tespit_eden')); ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Faaliyet Sorumlu</label>
      <!-- DİKKAT: migration’da falliyet_sorumlu yazıyor, formda da aynı olmalı -->
      <input type="text" name="falliyet_sorumlu" class="form-control" value="<?php echo e(old('falliyet_sorumlu')); ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Planlanan Tarih</label>
      <input type="date" name="planlanan_tarih" class="form-control" value="<?php echo e(old('planlanan_tarih')); ?>">
    </div>

    <button type="submit" class="btn btn-success">Kaydet</button>
    <a href="<?php echo e(route('uygunsuzluk.index')); ?>" class="btn btn-secondary">İptal</a>
  </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/uygunsuzluk/create.blade.php ENDPATH**/ ?>