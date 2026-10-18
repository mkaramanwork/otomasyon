

<?php $__env->startSection('title', 'Parametre Düzenle'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>Parametre Düzenle</h4>
    <form action="<?php echo e(route('parameters.update', $parameter->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="mb-3">
            <label>Numune Türü</label>
            <select name="sample_type" class="form-control" required>
                <option value="madeni yağ" <?php echo e($parameter->sample_type == 'madeni yağ' ? 'selected' : ''); ?>>Madeni Yağ</option>
                <option value="antifriz" <?php echo e($parameter->sample_type == 'antifriz' ? 'selected' : ''); ?>>Antifriz</option>
                <option value="gres" <?php echo e($parameter->sample_type == 'gres' ? 'selected' : ''); ?>>Gres</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Parametre</label>
            <input type="text" name="parameter" class="form-control" value="<?php echo e($parameter->parameter); ?>" required>
        </div>
        <div class="mb-3">
            <label>Metod (TR)</label>
            <input type="text" name="method_tr" class="form-control" value="<?php echo e($parameter->method_tr); ?>" required>
        </div>
        <div class="mb-3">
            <label>Metod (EN)</label>
            <input type="text" name="method_en" class="form-control" value="<?php echo e($parameter->method_en); ?>" required>
        </div>
        <div class="mb-3">
            <label>Metod (RU)</label>
            <input type="text" name="method_ru" class="form-control" value="<?php echo e($parameter->method_ru); ?>" required>
        </div>
        <div class="mb-3">
            <label>Birim</label>
            <input type="text" name="unit" class="form-control" value="<?php echo e($parameter->unit); ?>" required>
        </div>
        <div class="mb-3">
            <label>Kapsam</label>
            <select name="scope" class="form-control" required>
                <option value="TÜRKAK" <?php echo e($parameter->scope == 'TÜRKAK' ? 'selected' : ''); ?>>TÜRKAK</option>
                <option value="Kapsam Dışı" <?php echo e($parameter->scope == 'Kapsam Dışı' ? 'selected' : ''); ?>>Kapsam Dışı</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Belirsizlik</label>
            <input type="text" name="uncertainty" class="form-control" value="<?php echo e($parameter->uncertainty); ?>" required>
        </div>
        <button type="submit" class="btn btn-success">Güncelle</button>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/parameters/edit.blade.php ENDPATH**/ ?>