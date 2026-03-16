
<?php switch($type):

    
    case ('uygunsuzluk_new'): ?>
        <p>Merhaba,</p>
        <p>Yeni uygunsuzluk kaydı oluşturuldu:</p>
        <ul>
            <li><strong>Bölüm:</strong> <?php echo e($uygunsuzluk->bolum); ?></li>
            <li><strong>Tanım:</strong> <?php echo e($uygunsuzluk->tanim); ?></li>
            <li><strong>Bildirim Tarihi:</strong> <?php echo e($uygunsuzluk->bildirim_tarihi); ?></li>
            <li><strong>Durum:</strong> <?php echo e(ucfirst($uygunsuzluk->durum)); ?></li>
        </ul>
    <?php break; ?>

    
    <?php case ('uygunsuzluk_update'): ?>
        <p><strong><?php echo e($uygunsuzluk->bolum); ?></strong> bölümündeki uygunsuzluk kaydı güncellendi.</p>
        <p>
            Yeni Durum: <?php echo e(ucfirst($uygunsuzluk->durum)); ?> <br>
            Güncelleme Tarihi: <?php echo e($uygunsuzluk->updated_at); ?>

        </p>
    <?php break; ?>

    
    <?php case ('uygunsuzluk_close'): ?>
        <p><strong><?php echo e($uygunsuzluk->bolum); ?></strong> bölümündeki uygunsuzluk kapatıldı.</p>
        <p>
            Kapatma Tarihi: <?php echo e($uygunsuzluk->faaliyet_tarih); ?> <br>
            Kapatan: <?php echo e($uygunsuzluk->faaliyet_kapatan); ?>

        </p>
    <?php break; ?>

<?php endswitch; ?>

<hr>
<p style="font-size: 12px; color: gray;">
    Murat KARAMAN Wep Tarafından otomatik olarak gönderilmiştir.
</p><?php /**PATH C:\laragon\www\manufacturer\resources\views/uygunsuzluk/mail.blade.php ENDPATH**/ ?>