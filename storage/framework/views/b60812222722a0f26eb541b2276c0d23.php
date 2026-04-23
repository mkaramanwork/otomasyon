
<?php $__env->startSection('title','İlaveler'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4 class="mb-4">İlave Nedenler Listesi</h4>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <?php if($additions->isEmpty()): ?>
        <div class="alert alert-info">Henüz ilave kaydı bulunmamaktadır.</div>
    <?php else: ?>
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Numune Adı</th>
                            <th>Batch No</th>
                            <th>Tank No</th>
                            <th>İlave Nedeni</th>
                            <th>Parametreler</th>
                            <th>Uygunsuz Sonuç</th>
                            <th>Düzenleme</th>
                            <th>İlave Tarihi</th>
                            <th>Kaydeden</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $additions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $addition): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($loop->iteration); ?></td>
                                <td><?php echo e($addition->sample_name); ?></td>
                                <td><?php echo e($addition->batch_no); ?></td>
                                <td><?php echo e($addition->tank_no ?? '-'); ?></td>
                                <td><?php echo e($addition->reason); ?></td>
                                <td>
                                    <?php if($addition->parameters): ?>
                                        <?php $__currentLoopData = $addition->parameters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $param = \App\Models\LabParameter::find($p);
                                            ?>
                                            <span class="badge bg-info">
                                                <?php echo e($param->parameter ?? $param->name ?? $p); ?>

                                            </span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($addition->nonconformity_result ?? '-'); ?></td>
                                <td><?php echo e($addition->nonconformity_fix ?? '-'); ?></td>
                                <td><?php echo e($addition->reason_date?->format('d.m.Y H:i') ?? '-'); ?></td>
                                <td><?php echo e($addition->user->name ?? '-'); ?></td>
                                <td>
                                    <form action="<?php echo e(route('additions.destroyAddition', $addition->id)); ?>" method="POST" onsubmit="return confirm('Bu kaydı silmek istediğine emin misin?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Sil
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <div class="mt-3">
                    <?php echo e($additions->links()); ?>

                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/ilaveler/index.blade.php ENDPATH**/ ?>