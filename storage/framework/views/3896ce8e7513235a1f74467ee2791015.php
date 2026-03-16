
<?php $__env->startSection('title','Doküman Ekle'); ?>
<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>➕ Yeni Doküman Ekle</h4>

    <form action="<?php echo e(route('documents.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="mb-3">
            <label>Doküman No</label>
            <input type="text" name="doc_no" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Doküman Adı</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Revizyon No</label>
            <input type="number" name="revision_no" class="form-control" value="0" required>
        </div>

        <div class="mb-3">
            <label>Yayın Tarihi</label>
            <input type="date" name="publish_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Son Revizyon Tarihi</label>
            <input type="date" name="last_revision_date" class="form-control">
        </div>

        <div class="mb-3">
            <label>Gözden Geçirme Tarihi</label>
            <input type="date" name="review_date" class="form-control">
        </div>

        <div class="mb-3">
            <label>Revizyon Nedeni</label>
            <input type="text" name="revision_reason" class="form-control">
        </div>

        <div class="mb-3">
            <label>Durum</label>
            <select name="status" class="form-control">
                <option value="guncel">Güncel</option>
                <option value="arsiv">Arşiv</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Kaydet</button>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/documents/create.blade.php ENDPATH**/ ?>