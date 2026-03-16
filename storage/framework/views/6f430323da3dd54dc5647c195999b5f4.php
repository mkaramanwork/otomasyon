
<?php $__env->startSection('title', 'Formülasyonlar'); ?>

<?php $__env->startSection('content'); ?>
<div class="row mb-3">
    <div class="col-md-6">
        <h4>Formülasyonlar</h4>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?php echo e(route('formulas.create')); ?>" class="btn btn-success">
            <i class="fas fa-plus"></i> Yeni Formülasyon
        </a>
    </div>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="row mb-3">
    <div class="col-md-4">
        <input type="text" id="searchInput" class="form-control" placeholder="Formülasyon adına göre ara...">
    </div>
    <div class="col-md-4">
        <select id="typeFilter" class="form-control">
            <option value="">Tüm Tipler</option>
            <option value="karışım">Karışım</option>
            <option value="sıvı">Sıvı</option>
            <option value="toz">Toz</option>
        </select>
    </div>
    <div class="col-md-4">
        <select id="ownerFilter" class="form-control">
            <option value="">Tüm Ekleyenler</option>
            <option value="<?php echo e(auth()->id()); ?>">Sadece Benimkiler</option>
        </select>
    </div>
</div>

<table class="table table-bordered table-striped" id="formulaTable">
    <thead>
        <tr>
            <th>#</th>
            <th>Adı</th>
            <th>Tip</th>
            <th>Açıklama</th>
            <th>Ekleyen</th>
            <th>Ham Madde Sayısı / Tutar</th> <!-- ✅ revize -->
            <th>İşlemler</th>
        </tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $formulas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $formula): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr data-name="<?php echo e(strtolower($formula->name)); ?>" data-type="<?php echo e(strtolower($formula->type)); ?>" data-owner="<?php echo e($formula->added_by); ?>">
            <td><?php echo e($i + 1); ?></td>
            <td class="searchable"><?php echo e($formula->name); ?></td>
            <td><?php echo e(ucfirst($formula->type)); ?></td>
            <td><?php echo e(Str::limit($formula->description, 50)); ?></td>
            <td><?php echo e($formula->addedBy->name ?? 'Sistem'); ?></td>
            <td>
                <?php echo e($formula->details->count()); ?> adet  
                <br>
                <strong><?php echo e(number_format($formula->total_cost, 2, ',', '.')); ?> TL</strong>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modal-<?php echo e($formula->id); ?>">
                    <i class="fas fa-eye"></i> Reçeteyi Gör
                </button>
                <a href="<?php echo e(route('formulas.edit', $formula->id)); ?>" class="btn btn-sm btn-warning">
                    <i class="fas fa-edit"></i> Güncelle
                </a>
                <form action="<?php echo e(route('formulas.destroy', $formula->id)); ?>" method="POST" class="d-inline-block" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash"></i> Sil
                    </button>
                </form>
            </td>
        </tr>

        <!-- Modal -->
        <div class="modal fade" id="modal-<?php echo e($formula->id); ?>" tabindex="-1" aria-labelledby="modalLabel<?php echo e($formula->id); ?>" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel<?php echo e($formula->id); ?>">Reçete: <?php echo e($formula->name); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Tip:</strong> <?php echo e(ucfirst($formula->type)); ?></p>
                        <p><strong>Açıklama:</strong> <?php echo e($formula->description); ?></p>
                        <hr>
                        <h6>Ham Maddeler:</h6>
                        <ul>
                            <?php $total = 0; ?>
                            <?php $__currentLoopData = $formula->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $total += $detail->percentage; ?>
                                <li>
                                    <?php echo e($detail->rawMaterial->name); ?> - <?php echo e($detail->percentage); ?>% <?php echo e($detail->unit); ?>

                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                        <?php if(round($total, 2) !== 100): ?>
                            <div class="alert alert-warning mt-2">
                                Toplam oran %<?php echo e(round($total, 2)); ?> — %100 olmalı!
                            </div>
                        <?php endif; ?>

                        <hr>
                        <h6>Reçete Tutarı:</h6>
                        <p><strong><?php echo e(number_format($formula->total_cost, 2, ',', '.')); ?> TL</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
            <td colspan="7" class="text-center">Henüz formülasyon tanımlanmadı.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('javascript'); ?>
<script>
    function applyFilters() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const type = document.getElementById('typeFilter').value.toLowerCase();
        const owner = document.getElementById('ownerFilter').value;

        document.querySelectorAll('#formulaTable tbody tr').forEach(row => {
            const name = row.dataset.name;
            const rowType = row.dataset.type;
            const rowOwner = row.dataset.owner;

            const matchName = name.includes(search);
            const matchType = !type || rowType === type;
            const matchOwner = !owner || rowOwner === owner;

            row.style.display = (matchName && matchType && matchOwner) ? '' : 'none';
        });
    }

    document.getElementById('searchInput').addEventListener('keyup', applyFilters);
    document.getElementById('typeFilter').addEventListener('change', applyFilters);
    document.getElementById('ownerFilter').addEventListener('change', applyFilters);
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/formulas/index.blade.php ENDPATH**/ ?>