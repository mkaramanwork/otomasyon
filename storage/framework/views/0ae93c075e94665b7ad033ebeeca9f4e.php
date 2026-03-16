
<?php $__env->startSection('title','Doküman Detay'); ?>
<?php $__env->startSection('content'); ?>
<div class="container">
    <h4><?php echo e($document->title); ?> (<?php echo e($document->doc_no); ?>)</h4>

    <ul class="list-group mb-3">
        <li class="list-group-item"><strong>Revizyon No:</strong> R<?php echo e($document->revision_no); ?></li>
        <li class="list-group-item"><strong>Yayın Tarihi:</strong> <?php echo e($document->publish_date); ?></li>
        <li class="list-group-item"><strong>Son Revizyon Tarihi:</strong> <?php echo e($document->last_revision_date); ?></li>
        <li class="list-group-item"><strong>Gözden Geçirme Tarihi:</strong> <?php echo e($document->review_date); ?></li>
        <li class="list-group-item"><strong>Revizyon Nedeni:</strong> <?php echo e($document->revision_reason); ?></li>
        <li class="list-group-item"><strong>Kayıt Eden:</strong> <?php echo e($document->created_by); ?></li>
        <li class="list-group-item"><strong>Durum:</strong>
            <span class="badge bg-<?php echo e($document->status == 'guncel' ? 'success' : 'secondary'); ?>">
                <?php echo e(ucfirst($document->status)); ?>

            </span>
        </li>
    </ul>

    <!-- Revizyon Geçmişi -->
    <div class="card mb-4">
        <div class="card-header">📂 Revizyon Geçmişi</div>
        <div class="card-body">
            <table class="table table-sm table-bordered">
                <thead><tr><th>Revizyon</th><th>Tarih</th><th>Nedeni</th><th>Dosya</th></tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $document->revisions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>R<?php echo e($rev->revision_no); ?></td>
                            <td><?php echo e($rev->revision_date); ?></td>
                            <td><?php echo e($rev->change_note); ?></td>
                            <td><a href="<?php echo e(asset('storage/'.$rev->file_path)); ?>" target="_blank" class="btn btn-sm btn-outline-primary">Görüntüle</a></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="text-center">Henüz revizyon yok</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Yeni Revizyon Ekle -->
    <div class="card mb-4">
        <div class="card-header">➕ Yeni Revizyon Ekle</div>
        <div class="card-body">
            <form action="<?php echo e(route('documents.revision.store',$document->id)); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="mb-2">
                    <label>Revizyon Nedeni</label>
                    <input type="text" name="change_note" class="form-control">
                </div>
                <div class="mb-2">
                    <label>Dosya (PDF/DOCX)</label>
                    <input type="file" name="file" class="form-control" accept=".pdf,.docx" required>
                </div>
                <div class="mb-2">
                    <label>Revizyon Tarihi</label>
                    <input type="date" name="revision_date" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Gözden Geçirme Tarihi</label>
                    <input type="date" name="review_date" class="form-control">
                </div>
                <button type="submit" class="btn btn-success">Kaydet</button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/documents/show.blade.php ENDPATH**/ ?>