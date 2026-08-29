

<?php $__env->startSection('title','TDS Listesi'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>📑 TDS Listesi</h4>
        <div>
            <a href="<?php echo e(route('tds.create')); ?>" class="btn btn-success">➕ Yeni TDS</a>
            <a href="<?php echo e(route('tds.printAll')); ?>" class="btn btn-primary">🖨️ Tüm TDS’leri Yazdır</a>
            
        </div>
    </div>

    <table class="table table-striped table-hover table-bordered">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Ürün Adı</th>
                <th>Ürün Kodu</th>
                <th>Açıklama</th>
                <th>Oluşturulma</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($doc->id); ?></td>
                <td><?php echo e($doc->product_name); ?></td>
                <td><?php echo e($doc->product_code); ?></td>
                <td><?php echo e(Str::limit($doc->description,50)); ?></td>
                <td><?php echo e($doc->created_at->format('d.m.Y H:i')); ?></td>
                <td>
                    <a href="<?php echo e(route('tds.show',$doc->id)); ?>" class="btn btn-info btn-sm">🔍 Detay</a>
                    <a href="<?php echo e(route('tds.edit',$doc->id)); ?>" class="btn btn-warning btn-sm">✏️ Düzenle</a>
                    <a href="<?php echo e(route('tds.exportWord',$doc->id)); ?>" class="btn btn-primary btn-sm">📥 Word</a>
                    <a href="<?php echo e(route('tds.exportWordAlt',$doc->id)); ?>" class="btn btn-secondary btn-sm">📥 Word Taslak 2</a>
                    <form action="<?php echo e(route('tds.destroy',$doc->id)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Silmek istediğine emin misin?')">🗑️ Sil</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/tds/index.blade.php ENDPATH**/ ?>