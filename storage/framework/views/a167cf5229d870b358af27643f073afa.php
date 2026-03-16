

<?php $__env->startSection('title','Şikayet Detayı'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mt-4">
    <h3>📑 Şikayet Detayı: <?php echo e($complaint->complaint_no); ?></h3>

    <p><strong>Alınma Tarihi:</strong> <?php echo e($complaint->received_date); ?></p>
    <p><strong>Konu:</strong> <?php echo e($complaint->subject); ?></p>
    <p><strong>Tanım:</strong> <?php echo e($complaint->description); ?></p>
    <p><strong>Sahibi:</strong> <?php echo e($complaint->owner); ?></p>
    <p><strong>Telefon:</strong> <?php echo e($complaint->phone); ?></p>
    <p><strong>Email:</strong> <?php echo e($complaint->email); ?></p>
    <p><strong>Şikayeti Alan:</strong> <?php echo e($complaint->received_by); ?></p>
    <p><strong>Ürün:</strong> <?php echo e($complaint->product->name); ?></p>
    <p><strong>Kayıt Eden:</strong> <?php echo e($complaint->user->name); ?></p>
    <p><strong>Durum:</strong> <?php echo e($complaint->status); ?></p>

    <hr>

    <h5>📝 Değerlendirme</h5>
    <p><?php echo e($complaint->evaluation); ?></p>
    <p><strong>Tarih:</strong> <?php echo e($complaint->evaluation_date); ?></p>
    <p><strong>Yapan:</strong> <?php echo e($complaint->evaluated_by); ?></p>

    <h5>📊 Sonuç</h5>
    <p><?php echo e($complaint->result); ?></p>

    <h5>⚠️ DIF</h5>
    <p><strong>Açıldı mı?:</strong> <?php echo e($complaint->dif_opened); ?></p>
    <?php if($complaint->dif_opened == 'evet'): ?>
        <p><strong>Uygunsuzluk Açıklaması:</strong> <?php echo e($complaint->dif_description); ?></p>
    <?php endif; ?>

    <h5>🔄 Geri Bildirim</h5>
    <p><?php echo e($complaint->feedback); ?></p>

    <hr>

    <h5>📂 Dosyalar</h5>
    <ul>
        <?php $__currentLoopData = $complaint->files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>
                <a href="<?php echo e(asset('storage/'.$f->file_path)); ?>" target="_blank">
                    <?php echo e(strtoupper($f->stage)); ?> - <?php echo e($f->file_type); ?>

                </a>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>

    <h5>🛠️ Aksiyonlar</h5>
    <ul>
        <?php $__currentLoopData = $complaint->actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($a->action_description); ?> - <?php echo e($a->due_date); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>

    
    <div class="mt-3">
        <a href="<?php echo e(route('complaints.index')); ?>" class="btn btn-secondary">⬅️ Geri</a>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/complaints/show.blade.php ENDPATH**/ ?>