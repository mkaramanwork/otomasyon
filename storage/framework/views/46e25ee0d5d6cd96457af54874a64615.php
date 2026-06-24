
<?php $__env->startSection('title', 'Formülasyon Düzenle'); ?>

<?php $__env->startSection('content'); ?>
<div class="row mb-3">
    <div class="col-md-6"><h4>Formülasyon Düzenle</h4></div>
    <div class="col-md-6 text-right">
        <a href="<?php echo e(route('formulas.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>
</div>

<?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<form id="formula-form" action="<?php echo e(route('formulas.update', $formula->id)); ?>" method="POST">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label>Formülasyon Adı</label>
                <input type="text" name="name" class="form-control" value="<?php echo e($formula->name); ?>" required>
            </div>

            <div class="form-group">
                <label>Tip</label>
                <select name="type" class="form-control" required>
                    <option value="karışım" <?php echo e($formula->type == 'karışım' ? 'selected' : ''); ?>>Karışım</option>
                    <option value="sıvı" <?php echo e($formula->type == 'sıvı' ? 'selected' : ''); ?>>Sıvı</option>
                    <option value="toz" <?php echo e($formula->type == 'toz' ? 'selected' : ''); ?>>Toz</option>
                    <option value="OEM" <?php echo e($formula->type == 'OEM' ? 'selected' : ''); ?>>OEM</option>
                    <option value="orta_segment" <?php echo e($formula->type == 'orta_segment' ? 'selected' : ''); ?>>Orta Segment</option>
                    <option value="meet_requirement" <?php echo e($formula->type == 'meet_requirement' ? 'selected' : ''); ?>>Meet Requirement</option>
                    <option value="eco" <?php echo e($formula->type == 'eco' ? 'selected' : ''); ?>>Eco</option>
                </select>
            </div>

            <div class="form-group">
                <label>Açıklama</label>
                <textarea name="description" class="form-control" rows="3"><?php echo e($formula->description); ?></textarea>
            </div>

            <hr>
            <h5>Ham Madde Oranları</h5>
            <div id="material-container">
                <?php $__currentLoopData = $formula->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="row material-row mb-2">
                    <div class="col-md-5">
                        <select name="materials[<?php echo e($i); ?>][raw_material_id]" class="form-control" required>
                            <option value="">Ham madde seç</option>
                            <?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($mat->id); ?>" <?php echo e($detail->raw_material_id == $mat->id ? 'selected' : ''); ?>>
                                    <?php echo e($mat->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" step="0.01" name="materials[<?php echo e($i); ?>][percentage]" class="form-control"
                            value="<?php echo e($detail->percentage); ?>" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="materials[<?php echo e($i); ?>][unit]" class="form-control"
                            value="<?php echo e($detail->unit); ?>">
                    </div>
                    <div class="col-md-1 text-right">
                        <button type="button" class="btn btn-danger btn-sm remove-material">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <button type="button" id="add-material" class="btn btn-sm btn-info mt-2">
                <i class="fas fa-plus"></i> Ham Madde Ekle
            </button>
        </div>

        <div class="card-footer text-right">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Güncelle
            </button>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('javascript'); ?>
<script>
    let index = <?php echo e(count($formula->details)); ?>;
    const rawMaterials = <?php echo json_encode($materials, 15, 512) ?>;

    document.getElementById('add-material').addEventListener('click', function () {
        const container = document.getElementById('material-container');
        const row = document.createElement('div');
        row.classList.add('row', 'material-row', 'mb-2');

        let options = '<option value="">Ham madde seç</option>';
        rawMaterials.forEach(mat => {
            options += `<option value="${mat.id}">${mat.name}</option>`;
        });

        row.innerHTML = `
            <div class="col-md-5">
                <select name="materials[${index}][raw_material_id]" class="form-control" required>
                    ${options}
                </select>
            </div>
            <div class="col-md-3">
                <input type="number" step="0.01" name="materials[${index}][percentage]" class="form-control" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="materials[${index}][unit]" class="form-control">
            </div>
            <div class="col-md-1 text-right">
                <button type="button" class="btn btn-danger btn-sm remove-material">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
        index++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-material')) {
            e.target.closest('.material-row').remove();
        }
    });

    document.getElementById('formula-form').addEventListener('submit', function (e) {
        const percentages = document.querySelectorAll('input[name^="materials"][name$="[percentage]"]');
        let total = 0;

        percentages.forEach(input => {
            const val = parseFloat(input.value);
            if (!isNaN(val)) total += val;
        });

        total = Math.round(total * 100) / 100;

        if (total !== 100) {
            e.preventDefault();
            alert("Ham madde oranlarının toplamı %100 olmalıdır.");
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/formulas/edit.blade.php ENDPATH**/ ?>