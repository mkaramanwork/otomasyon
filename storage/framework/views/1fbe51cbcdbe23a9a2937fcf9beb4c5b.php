

<?php $__env->startSection('title','Numune Güncelle'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>✏️ Numune Güncelle</h4>

    <form action="<?php echo e(route('samples.update',$sample->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <!-- Numune bilgileri (readonly) -->
        <div class="mb-3">
            <label class="form-label">Firma</label>
            <input type="text" class="form-control" value="<?php echo e($sample->company_name); ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Batch No</label>
            <input type="text" class="form-control" value="<?php echo e($sample->batch_no); ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Talep No</label>
            <input type="text" class="form-control" value="<?php echo e($sample->request_no); ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Numune Türü</label>
            <input type="text" class="form-control" value="<?php echo e($sample->sample_type); ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Alındığı Yer</label>
            <input type="text" class="form-control" value="<?php echo e($sample->taken_place); ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Alan Kişi</label>
            <input type="text" class="form-control" value="<?php echo e($sample->taken_by); ?>" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Alınma Tarihi</label>
            <input type="text" class="form-control" value="<?php echo e($sample->taken_at); ?>" readonly>
        </div>

        <!-- Parametreler -->
        <h5 class="mt-4">📊 Parametreler</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Seç</th>
                    <th>Numune Türü</th>
                    <th>Parametre</th>
                    <th>Metot (TR)</th>
                    <th>Birim</th>
                    <th>Kapsam</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $parameters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $param): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="selected_parameters[]" value="<?php echo e($param->id); ?>"
                                   <?php if($sample->parameters->contains($param->id)): ?> checked <?php endif; ?>>
                        </td>
                        <td><?php echo e($param->sample_type); ?></td>
                        <td><?php echo e($param->parameter); ?></td>
                        <td><?php echo e($param->method_tr); ?></td>
                        <td><?php echo e($param->unit); ?></td>
                        <td><?php echo e($param->scope); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Parametreleri Güncelle
            </button>
            <a href="<?php echo e(route('samples.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Geri Dön
            </a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/sample_acceptances/edit.blade.php ENDPATH**/ ?>