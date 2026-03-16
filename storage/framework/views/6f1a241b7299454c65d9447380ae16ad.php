<p>Merhaba,</p>
<p><?php echo e($document->title); ?> dokümanı için yeni revizyon eklendi:</p>
<ul>
    <li><strong>Doküman No:</strong> <?php echo e($document->doc_no); ?></li>
    <li><strong>Revizyon No:</strong> R<?php echo e($revision->revision_no); ?></li>
    <li><strong>Revizyon Tarihi:</strong> <?php echo e($revision->revision_date); ?></li>
    <li><strong>Gözden Geçirme Tarihi:</strong> <?php echo e($revision->review_date); ?></li>
    <li><strong>Nedeni:</strong> <?php echo e($revision->change_note); ?></li>
</ul>
<p>Dosya: <a href="<?php echo e(asset('storage/'.$revision->file_path)); ?>">Görüntüle</a></p>
<p>Güncelleyen: <?php echo e($revision->updated_by); ?></p>
<p>Murat KARAMAN ERP</p><?php /**PATH C:\laragon\www\manufacturer\resources\views/documents/mail_rev.blade.php ENDPATH**/ ?>