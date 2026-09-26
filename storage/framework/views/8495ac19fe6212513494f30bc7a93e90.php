

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>Dış Kaynaklı Dokümanlar</h4>

    <!-- Yeni doküman ekleme -->
    <a href="<?php echo e(route('external-documents.create')); ?>" class="btn btn-primary mb-3">Yeni Doküman</a>

    <!-- Listeye Aktar butonu -->
    <a href="<?php echo e(route('external-documents.exportWord')); ?>" class="btn btn-success mb-3">
        Listeye Aktar (Word)
    </a>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Sıra No</th>
                <th>Kod</th>
                <th>Başlık</th>
                <th>Kurum</th>
                <th>Revizyon</th>
                <th>Yayın Tarihi</th>
                <th>Gözden Geçirilme Tarihi</th>
                <th>URL</th>
                <th>Dosya</th>
                <th>Kayıt Eden</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $docs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($doc->sequence_no); ?></td>
                <td><?php echo e($doc->document_code); ?></td>
                <td><?php echo e($doc->title); ?></td>
                <td><?php echo e($doc->institution); ?></td>
                <td><?php echo e($doc->revision_no); ?></td>
                <td><?php echo e($doc->publish_date); ?></td>
                <td><?php echo e($doc->review_date); ?></td>
                <td><?php echo e($doc->url); ?></td>
                <td>
                    <a href="<?php echo e(asset('storage/'.$doc->file_path)); ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                        Görüntüle
                    </a>
                </td>
                <td><?php echo e($doc->user->name ?? '-'); ?></td>
                <td>
                    <a href="<?php echo e(route('external-documents.show',$doc->id)); ?>" class="btn btn-info btn-sm">Detay</a>
                    <a href="<?php echo e(route('external-documents.edit',$doc->id)); ?>" class="btn btn-warning btn-sm">Düzenle</a>
                    <form action="<?php echo e(route('external-documents.destroy',$doc->id)); ?>" method="POST" style="display:inline-block;">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Silmek istediğine emin misin?')">
                            Sil
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/external_documents/index.blade.php ENDPATH**/ ?>