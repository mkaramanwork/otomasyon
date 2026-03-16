

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>Yeni Dış Kaynaklı Doküman</h4>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                   <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('external-documents.store')); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>

        <div class="mb-3">
            <label class="form-label">Sıra No</label>
            <input type="number" name="sequence_no" class="form-control" value="<?php echo e($nextSeq); ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Doküman Kodu</label>
            <input type="text" name="document_code" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Doküman Adı</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Kurum</label>
            <input type="text" name="institution" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Revizyon No</label>
            <input type="text" name="revision_no" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Yayın Tarihi</label>
            <input type="date" name="publish_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Gözden Geçirilme Tarihi</label>
            <input type="date" name="review_date" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Kaynak URL</label>
            <input type="text" name="url" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Dosya (PDF, Word, PowerPoint)</label>
            <input type="file" name="file" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Kaydet</button>
        <a href="<?php echo e(route('external-documents.index')); ?>" class="btn btn-secondary">İptal</a>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/external_documents/create.blade.php ENDPATH**/ ?>