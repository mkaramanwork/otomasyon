<?php $__env->startSection('title', 'SDS Formül Analizi'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1">🧪 SDS Formül Analizi</h3>
            <div class="text-muted">
                Formülden hammadde SDS bileşenlerini ve mevzuat verilerini toplayarak
                efektif konsantrasyon kontrolü yapar.
            </div>
        </div>

        <a href="<?php echo e(route('sds.raw-materials.index')); ?>"
           class="btn btn-outline-secondary">
            ← SDS Yönetimi
        </a>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <strong>Formül Ara</strong>
        </div>

        <div class="card-body">
            <form method="GET"
                  action="<?php echo e(route('sds.formula-analysis.index')); ?>">
                <div class="row g-2">
                    <div class="col-md-9">
                        <input type="text"
                               name="q"
                               class="form-control"
                               value="<?php echo e($search); ?>"
                               placeholder="Formül adı veya Formula ID ara...">
                    </div>

                    <div class="col-md-2 d-grid">
                        <button type="submit"
                                class="btn btn-primary">
                            Ara
                        </button>
                    </div>

                    <div class="col-md-1 d-grid">
                        <a href="<?php echo e(route('sds.formula-analysis.index')); ?>"
                           class="btn btn-outline-secondary">
                            Temizle
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-dark text-white">
            <strong>Formüller</strong>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width:100px;">ID</th>
                            <th>Formül Adı</th>
                            <th style="width:180px;">Tip</th>
                            <th style="width:260px;">İşlem</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $formulas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $formula): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($formula->id); ?></td>
                            <td>
                                <strong><?php echo e($formula->name); ?></strong>
                            </td>
                            <td><?php echo e($formula->type ?? '-'); ?></td>
                            <td>
                                <a href="<?php echo e(route('sds.formula-analysis.show', [
                                        'formula' => $formula->id,
                                        'regulation_profile' => 'TR_KKDIK_SEA'
                                    ])); ?>"
                                   class="btn btn-sm btn-success">
                                    TR Analiz Et
                                </a>

                                <a href="<?php echo e(route('sds.formula-analysis.show', [
                                        'formula' => $formula->id,
                                        'regulation_profile' => 'EU_REACH_CLP'
                                    ])); ?>"
                                   class="btn btn-sm btn-outline-primary">
                                    EU Analiz Et
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4"
                                class="text-center text-muted py-4">
                                Formül bulunamadı.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if($formulas->hasPages()): ?>
            <div class="card-footer">
                <?php echo e($formulas->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/sds/formula-analysis/index.blade.php ENDPATH**/ ?>