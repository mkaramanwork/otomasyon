
<?php switch($type):

    
    case ('new'): ?>
        <p>Merhaba,</p>
        <p>Yeni personel eklendi:</p>
        <ul>
            <li>Adı: <?php echo e($personnel->name); ?></li>
            <li>Ünvanı: <?php echo e($personnel->title); ?></li>
            <li>Görevi: <?php echo e($personnel->duty); ?></li>
            <li>İşe Başlama Tarihi: <?php echo e($personnel->start_date); ?></li>
            <li>Vekili: <?php echo e($personnel->deputy); ?></li>
        </ul>
    <?php break; ?>

    
    <?php case ('orientation'): ?>
        <p><?php echo e($personnel->name); ?> için yeni oryantasyon planı oluşturuldu.</p>
        <p>Bölüm: <?php echo e($plan->department); ?><br>
        Amaç: <?php echo e($plan->purpose); ?></p>
    <?php break; ?>

    
    <?php case ('activity'): ?>
        <p>Oryantasyon faaliyeti eklendi:</p>
        <ul>
            <li>Faaliyet: <?php echo e($activity->activity); ?></li>
            <li>Eğitimi Veren: <?php echo e($activity->trainer); ?></li>
            <li>Başlangıç: <?php echo e($activity->start_date); ?></li>
            <li>Bitiş: <?php echo e($activity->end_date); ?></li>
            <li>Sonuç: <?php echo e($activity->result); ?></li>
        </ul>
    <?php break; ?>

    
    <?php case ('complete'): ?>
        <p><?php echo e($plan->personnel->name); ?> için oryantasyon tamamlandı.</p>
        <p>Değerlendirme Tarihi: <?php echo e($plan->evaluation_date); ?><br>
        Onay Tarihi: <?php echo e($plan->approval_date); ?></p>
    <?php break; ?>

    
    <?php case ('competency'): ?>
        <p><?php echo e($personnel->name); ?> için yeni yetkinlik ataması yapıldı.</p>
        <ul>
            <?php $__currentLoopData = $personnel->competencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($c->method); ?> (Seviye: <?php echo e($c->pivot->level); ?>, Tarih: <?php echo e($c->pivot->granted_at); ?>)</li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    <?php break; ?>

<?php endswitch; ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/personnel/mail.blade.php ENDPATH**/ ?>