<p>Merhaba,</p>
<p>Yeni doküman eklendi:</p>
<ul>
    <li><strong>Doküman No:</strong> <?php echo e($document->doc_no); ?></li>
    <li><strong>Başlık:</strong> <?php echo e($document->title); ?></li>
    <li><strong>Revizyon No:</strong> R<?php echo e($document->revision_no); ?></li>
    <li><strong>Yayın Tarihi:</strong> <?php echo e($document->publish_date); ?></li>
    <li><strong>Durum:</strong> <?php echo e(ucfirst($document->status)); ?></li>
</ul>
<p>Ekleyen: <?php echo e($document->created_by); ?></p>
<p>Murat KARAMAN ERP</p><?php /**PATH C:\laragon\www\manufacturer\resources\views/documents/mail.blade.php ENDPATH**/ ?>