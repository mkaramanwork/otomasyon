

<?php $__env->startSection('title', 'Uygunsuzluk Düzenle'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
  <h4 class="mb-4">✏️ Uygunsuzluk Düzenle</h4>

  <?php if($errors->any()): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="<?php echo e(route('uygunsuzluk.update', $uygunsuzluk->id)); ?>" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <!-- Temel Alanlar -->
    <div class="mb-3">
      <label class="form-label">Bölüm</label>
      <input type="text" name="bolum" class="form-control" value="<?php echo e(old('bolum', $uygunsuzluk->bolum)); ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Tanım</label>
      <input type="text" name="tanim" class="form-control" value="<?php echo e(old('tanim', $uygunsuzluk->tanim)); ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Bildirim Tarihi</label>
      <input type="date" name="bildirim_tarihi" class="form-control" value="<?php echo e(old('bildirim_tarihi', $uygunsuzluk->bildirim_tarihi)); ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Uygunsuzluk Sınıfı</label>
      <input type="text" name="uy_sinif" class="form-control" value="<?php echo e(old('uy_sinif', $uygunsuzluk->uy_sinif)); ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Durum</label>
      <select name="durum" class="form-select">
        <option value="open" <?php if($uygunsuzluk->durum=='open'): ?> selected <?php endif; ?>>Open</option>
        <option value="in_progress" <?php if($uygunsuzluk->durum=='in_progress'): ?> selected <?php endif; ?>>In Progress</option>
        <option value="closed" <?php if($uygunsuzluk->durum=='closed'): ?> selected <?php endif; ?>>Closed</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Kaynak</label>
      <input type="text" name="kaynak" class="form-control" value="<?php echo e(old('kaynak', $uygunsuzluk->kaynak)); ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Açıklama</label>
      <textarea name="uy_aciklama" class="form-control"><?php echo e(old('uy_aciklama', $uygunsuzluk->uy_aciklama)); ?></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Yapılacak Faaliyet</label>
      <input type="text" name="yapilacak_faaliyet" class="form-control" value="<?php echo e(old('yapilacak_faaliyet', $uygunsuzluk->yapilacak_faaliyet)); ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Kök Neden</label>
      <input type="text" name="kaplam_kok" class="form-control" value="<?php echo e(old('kaplam_kok', $uygunsuzluk->kaplam_kok)); ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Düzeltici Faaliyet</label>
      <input type="text" name="duzeltici_faaliyet" class="form-control" value="<?php echo e(old('duzeltici_faaliyet', $uygunsuzluk->duzeltici_faaliyet)); ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Tespit Eden</label>
      <input type="text" name="tespit_eden" class="form-control" value="<?php echo e(old('tespit_eden', $uygunsuzluk->tespit_eden)); ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Faaliyet Sorumlu</label>
      <input type="text" name="falliyet_sorumlu" class="form-control" value="<?php echo e(old('falliyet_sorumlu', $uygunsuzluk->falliyet_sorumlu)); ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Planlanan Tarih</label>
      <input type="date" name="planlanan_tarih" class="form-control" value="<?php echo e(old('planlanan_tarih', $uygunsuzluk->planlanan_tarih)); ?>">
    </div>

    <!-- Accordion Yapısı -->
    <div class="accordion mt-4" id="uygunsuzlukAccordion">

      <!-- Faaliyet Değerlendirme -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingFaaliyet">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFaaliyet" aria-expanded="true" aria-controls="collapseFaaliyet">
            📌 Faaliyet Değerlendirme
          </button>
        </h2>
        <div id="collapseFaaliyet" class="accordion-collapse collapse show" aria-labelledby="headingFaaliyet" data-bs-parent="#uygunsuzlukAccordion">
          <div class="accordion-body">
            <div class="mb-3">
              <label class="form-label">Faaliyet Değerlendirme</label>
              <input type="text" name="faaliyet_degerlendirme" class="form-control" value="<?php echo e(old('faaliyet_degerlendirme', $uygunsuzluk->faaliyet_degerlendirme)); ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Faaliyet Açıklama</label>
              <textarea name="faaliyet_aciklama" class="form-control"><?php echo e(old('faaliyet_aciklama', $uygunsuzluk->faaliyet_aciklama)); ?></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Faaliyet Kapatan</label>
              <input type="text" name="faaliyet_kapatan" class="form-control" value="<?php echo e(old('faaliyet_kapatan', $uygunsuzluk->faaliyet_kapatan)); ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Faaliyet Tarih</label>
              <input type="date" name="faaliyet_tarih" class="form-control" value="<?php echo e(old('faaliyet_tarih', $uygunsuzluk->faaliyet_tarih)); ?>">
            </div>
          </div>
        </div>
      </div>

      <!-- Etkinlik Değerlendirme -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingEtkinlik">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEtkinlik" aria-expanded="false" aria-controls="collapseEtkinlik">
            📌 Etkinlik Değerlendirme
          </button>
        </h2>
        <div id="collapseEtkinlik" class="accordion-collapse collapse" aria-labelledby="headingEtkinlik" data-bs-parent="#uygunsuzlukAccordion">
          <div class="accordion-body">
            <div class="mb-3">
              <label class="form-label">Etkinlik Değerlendirme</label>
              <input type="text" name="etkinlik_degerlendirme" class="form-control" value="<?php echo e(old('etkinlik_degerlendirme', $uygunsuzluk->etkinlik_degerlendirme)); ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Etkinlik Açıklama</label>
              <textarea name="etkinlik_aciklama" class="form-control"><?php echo e(old('etkinlik_aciklama', $uygunsuzluk->etkinlik_aciklama)); ?></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Etkinlik Kapatan</label>
              <input type="text" name="etkinlik_kapatan" class="form-control" value="<?php echo e(old('etkinlik_kapatan', $uygunsuzluk->etkinlik_kapatan)); ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Etkinlik Tarih</label>
              <input type="date" name="etkinlik_tarih" class="form-control" 
                     value="<?php echo e(old('etkinlik_tarih', $uygunsuzluk->etkinlik_tarih)); ?>">
            </div>
          </div>
        </div>
      </div> <!-- accordion-item -->
    </div> <!-- accordion -->

    <!-- Dosya Yükleme Alanı -->
    <hr class="mt-4">
    <h5 class="mt-4">📎 Objektif Kayıt Yükleme (Faaliyet Kapatıldığında)</h5>
    <div class="mb-3">
      <label class="form-label">Dosya Yükle</label>
      <input type="file" name="file" class="form-control">
      <?php if($uygunsuzluk->file_name): ?>
        <p class="mt-2">Mevcut Dosya: 
          <a href="<?php echo e(asset('uploads/'.$uygunsuzluk->file_name)); ?>" target="_blank">
            <?php echo e($uygunsuzluk->file_name); ?>

          </a>
        </p>
      <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary mt-4">Güncelle</button>
    <a href="<?php echo e(route('uygunsuzluk.index')); ?>" class="btn btn-secondary mt-4">İptal</a>
  </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/uygunsuzluk/edit.blade.php ENDPATH**/ ?>