
<?php $__env->startSection('title','Doküman Yönetimi'); ?>
<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>📑 Doküman Listesi</h4>
    <a href="<?php echo e(route('documents.create')); ?>" class="btn btn-primary mb-3">➕ Doküman Ekle</a>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Adı</th>
                <th>Revizyon</th>
                <th>Yayın</th>
                <th>Son Revizyon</th>
                <th>Gözden Geçirme</th>
                <th>Durum</th>
                <th>Güncel Dosya</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($doc->doc_no); ?></td>
                <td><?php echo e($doc->title); ?></td>
                <td>R<?php echo e($doc->revision_no); ?></td>
                <td><?php echo e($doc->publish_date); ?></td>
                <td><?php echo e($doc->last_revision_date); ?></td>
                <td><?php echo e($doc->review_date); ?></td>
                <td>
                    <span class="badge bg-<?php echo e($doc->status == 'guncel' ? 'success' : 'secondary'); ?>">
                        <?php echo e(ucfirst($doc->status)); ?>

                    </span>
                </td>
                <td>
                    <?php if($doc->latestRevision): ?>
                        <a href="<?php echo e(asset('storage/'.$doc->latestRevision->file_path)); ?>" 
                           target="_blank" class="btn btn-sm btn-outline-primary">
                           Güncel Dosya
                        </a>
                    <?php else: ?>
                        <span class="text-muted">Dosya yok</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="<?php echo e(route('documents.show',$doc->id)); ?>" class="btn btn-info btn-sm">Detay</a>
                    <a href="<?php echo e(route('documents.edit',$doc->id)); ?>" class="btn btn-warning btn-sm">Güncelle</a>
                    <form action="<?php echo e(route('documents.destroy',$doc->id)); ?>" method="POST" style="display:inline;" 
                          onsubmit="return confirm('Bu dokümanı silmek istediğine emin misin?')">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="9" class="text-center">Henüz doküman yok</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/documents/index.blade.php ENDPATH**/ ?>