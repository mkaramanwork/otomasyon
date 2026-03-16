

<?php $__env->startSection('title', 'Uygunsuzluk Detayı'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
  <h4 class="mb-4">📄 Uygunsuzluk Detayı</h4>

  <table class="table table-bordered">
    <tr><th>ID</th><td><?php echo e($uygunsuzluk->id); ?></td></tr>
    <tr><th>Bölüm</th><td><?php echo e($uygunsuzluk->bolum); ?></td></tr>
    <tr><th>Tanım</th><td><?php echo e($uygunsuzluk->tanim); ?></td></tr>
    <tr><th>Durum</th>
      <td>
        <span class="badge bg-<?php echo e($uygunsuzluk->durum === 'closed' ? 'success' : 'warning'); ?>">
          <?php echo e(ucfirst($uygunsuzluk->durum)); ?>

        </span>
      </td>
    </tr>
    <tr><th>Bildirim Tarihi</th><td><?php echo e($uygunsuzluk->bildirim_tarihi); ?></td></tr>
    <tr><th>Kaynak</th><td><?php echo e($uygunsuzluk->kaynak); ?></td></tr>
    <tr><th>Açıklama</th><td><?php echo e($uygunsuzluk->uy_aciklama); ?></td></tr>
    <tr><th>Yapılacak Faaliyet</th><td><?php echo e($uygunsuzluk->yapilacak_faaliyet); ?></td></tr>
    <tr><th>Kök Neden</th><td><?php echo e($uygunsuzluk->kaplam_kok); ?></td></tr>
    <tr><th>Düzeltici Faaliyet</th><td><?php echo e($uygunsuzluk->duzeltici_faaliyet); ?></td></tr>
    <tr><th>Tespit Eden</th><td><?php echo e($uygunsuzluk->tespit_eden); ?></td></tr>
    <tr><th>Faaliyet Sorumlu</th><td><?php echo e($uygunsuzluk->falliyet_sorumlu); ?></td></tr>
    <tr><th>Planlanan Tarih</th><td><?php echo e($uygunsuzluk->planlanan_tarih); ?></td></tr>
    <tr><th>Faaliyet Değerlendirme</th><td><?php echo e($uygunsuzluk->faaliyet_degerlendirme); ?></td></tr>
    <tr><th>Faaliyet Açıklama</th><td><?php echo e($uygunsuzluk->faaliyet_aciklama); ?></td></tr>
    <tr><th>Faaliyet Kapatan</th><td><?php echo e($uygunsuzluk->faaliyet_kapatan); ?></td></tr>
    <tr><th>Faaliyet Tarih</th><td><?php echo e($uygunsuzluk->faaliyet_tarih); ?></td></tr>
    <tr><th>Etkinlik Değerlendirme</th><td><?php echo e($uygunsuzluk->etkinlik_degerlendirme); ?></td></tr>
    <tr><th>Etkinlik Açıklama</th><td><?php echo e($uygunsuzluk->etkinlik_aciklama); ?></td></tr>
    <tr><th>Etkinlik Kapatan</th><td><?php echo e($uygunsuzluk->etkinlik_kapatan); ?></td></tr>
    <tr><th>Etkinlik Tarih</th><td><?php echo e($uygunsuzluk->etkinlik_tarih); ?></td></tr>

    <!-- Dosya Alanı -->
    <tr>
      <th>Objektif Kayıt</th>
      <td>
        <?php if($uygunsuzluk->file_name): ?>
          <a href="<?php echo e(asset('uploads/'.$uygunsuzluk->file_name)); ?>" target="_blank" class="btn btn-sm btn-outline-info">
            📎 Dosyayı Görüntüle
          </a>
          <p class="mt-2"><small>Yüklendi: <?php echo e($uygunsuzluk->uploaded_on); ?></small></p>
        <?php else: ?>
          <span class="text-muted">Dosya yüklenmemiş</span>
        <?php endif; ?>
      </td>
    </tr>

    <tr><th>Oluşturulma</th><td><?php echo e($uygunsuzluk->created_at); ?></td></tr>
    <tr><th>Güncellenme</th><td><?php echo e($uygunsuzluk->updated_at); ?></td></tr>
  </table>

  <div class="mt-3">
    <a href="<?php echo e(route('uygunsuzluk.index')); ?>" class="btn btn-secondary">⬅️ Listeye Dön</a>
    <a href="<?php echo e(route('uygunsuzluk.edit', $uygunsuzluk->id)); ?>" class="btn btn-primary">✏️ Düzenle</a>
    <form action="<?php echo e(route('uygunsuzluk.destroy', $uygunsuzluk->id)); ?>" method="POST" 
          onsubmit="return confirm('Bu kaydı silmek istediğinize emin misiniz?')" style="display:inline;">
      <?php echo csrf_field(); ?>
      <?php echo method_field('DELETE'); ?>
      <button type="submit" class="btn btn-danger">🗑️ Sil</button>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/uygunsuzluk/show.blade.php ENDPATH**/ ?>