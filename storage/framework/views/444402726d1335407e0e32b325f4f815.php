<?php $__env->startSection('title','Hammadde SDS Yönetimi'); ?>

<?php $__env->startSection('content'); ?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <h4 class="mb-0">
                🧪 Hammadde SDS Yönetimi
            </h4>

            <small class="text-muted">
                Mevcut hammaddelere ait Güvenlik Bilgi Formu kayıtları
            </small>

        </div>

        <div class="d-flex gap-2 flex-wrap">

                <a href="<?php echo e(route('sds.regulatory.index')); ?>"
                class="btn btn-dark">

                    🌐 Mevzuat Veritabanı

                </a>

                <a href="<?php echo e(route('sds.regulatory.import')); ?>"
                class="btn btn-primary">

                    🔄 Mevzuat Güncelleme

                </a>

        </div>

    </div>


    <?php if(session('success')): ?>

        <div class="alert alert-success alert-dismissible fade show">

            <?php echo e(session('success')); ?>


            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    <?php endif; ?>


    <?php if(session('error')): ?>

        <div class="alert alert-danger alert-dismissible fade show">

            <?php echo e(session('error')); ?>


            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    <?php endif; ?>


    <?php if($errors->any()): ?>

        <div class="alert alert-danger alert-dismissible fade show">

            <strong>
                İşlem gerçekleştirilemedi.
            </strong>

            <ul class="mb-0 mt-2">

                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <li>
                        <?php echo e($error); ?>

                    </li>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </ul>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    <?php endif; ?>


    <div class="card mb-3">

        <div class="card-header">

            <strong>
                🔎 Hammadde Ara
            </strong>

        </div>

        <div class="card-body">

            <form method="GET"
                  action="<?php echo e(route('sds.raw-materials.index')); ?>">

                <div class="row g-2">

                    <div class="col-md-9">

                        <div class="input-group">

                            <span class="input-group-text">
                                🔎
                            </span>

                            <input type="text"
                                   name="q"
                                   class="form-control"
                                   value="<?php echo e($search); ?>"
                                   placeholder="Hammadde adı ara...">

                        </div>

                    </div>

                    <div class="col-md-3">

                        <div class="d-flex gap-2">

                            <button type="submit"
                                    class="btn btn-primary w-100">

                                Ara

                            </button>

                            <a href="<?php echo e(route('sds.raw-materials.index')); ?>"
                               class="btn btn-outline-secondary w-100">

                                Temizle

                            </a>

                        </div>

                    </div>

                </div>

                <div class="mt-2">

                    <small class="text-muted">

                        Toplam kayıt:

                        <strong>
                            <?php echo e($rawMaterials->total()); ?>

                        </strong>

                    </small>

                </div>

            </form>

        </div>

    </div>


    <div class="card">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-striped table-hover table-bordered mb-0">

                    <thead class="table-dark">

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                Hammadde
                            </th>

                            <th>
                                Birim
                            </th>

                            <th>
                                SDS Durumu
                            </th>

                            <th>
                                Tedarikçi
                            </th>

                            <th>
                                SDS Tarihi
                            </th>

                            <th>
                                Revizyon
                            </th>

                            <th>
                                Dil
                            </th>

                            <th style="min-width:220px;">
                                İşlemler
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php $__empty_1 = true; $__currentLoopData = $rawMaterials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rawMaterial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                            <?php
                                $sds = $rawMaterial->currentSdsProfile;
                            ?>

                            <tr>

                                <td>
                                    <?php echo e($rawMaterial->id); ?>

                                </td>

                                <td>

                                    <strong>
                                        <?php echo e($rawMaterial->name); ?>

                                    </strong>

                                </td>

                                <td>

                                    <?php echo e($rawMaterial->unit ?? '-'); ?>


                                </td>

                                <td>

                                    <?php if(!$sds): ?>

                                        <span class="badge text-bg-secondary">
                                            SDS Yok
                                        </span>

                                    <?php elseif($sds->is_verified): ?>

                                        <span class="badge text-bg-success">
                                            ✓ Doğrulandı
                                        </span>

                                    <?php else: ?>

                                        <span class="badge text-bg-warning">
                                            ⚠ Kontrol Bekliyor
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <?php echo e($sds?->supplier_name ?? '-'); ?>


                                </td>

                                <td>

                                    <?php if($sds?->supplier_sds_date): ?>

                                        <?php echo e($sds->supplier_sds_date->format('d.m.Y')); ?>


                                    <?php else: ?>

                                        -

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <?php echo e($sds?->supplier_sds_revision
                                        ?? $sds?->supplier_sds_version
                                        ?? '-'); ?>


                                </td>

                                <td>

                                    <?php if($sds): ?>

                                        <span class="badge text-bg-dark">
                                            <?php echo e(strtoupper($sds->sds_language ?? '-')); ?>

                                        </span>

                                    <?php else: ?>

                                        -

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <div class="d-flex gap-1 flex-wrap">

                                        <?php if($sds): ?>

                                            <a href="<?php echo e(route('sds.raw-materials.show', [
                                                    $rawMaterial->id,
                                                    $sds->id
                                                ])); ?>"
                                            class="btn btn-info btn-sm">

                                                🔍 SDS Detay

                                            </a>

                                            <a href="<?php echo e(route('sds.raw-materials.edit', [$rawMaterial->id, $sds->id])); ?>" class="btn btn-warning btn-sm">✏️ Yönet</a>
                                            <a href="<?php echo e(route('sds.raw-materials.history', $rawMaterial->id)); ?>" class="btn btn-outline-secondary btn-sm">🕘 Geçmiş</a>

                                        <?php else: ?>

                                            <a href="<?php echo e(route('sds.raw-materials.create', $rawMaterial->id)); ?>"
                                               class="btn btn-success btn-sm">

                                                ➕ SDS Oluştur

                                            </a>

                                        <?php endif; ?>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                            <tr>

                                <td colspan="9"
                                    class="text-center text-muted py-4">

                                    Hammadde kaydı bulunamadı.

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>


        <?php if($rawMaterials->hasPages()): ?>

            <div class="card-footer">

                <?php echo e($rawMaterials->links()); ?>


            </div>

        <?php endif; ?>

    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/sds/index.blade.php ENDPATH**/ ?>