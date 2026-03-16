
<?php $__env->startSection('title','Cihaz Detay'); ?>
<?php $__env->startSection('content'); ?>
<div class="container">
    <h4><?php echo e($equipment->name); ?> (<?php echo e($equipment->tag); ?>)</h4>

    <!-- QR Kod -->
    <div class="mb-3">
        <?php echo QrCode::size(150)->generate(route('equipment.show',$equipment->id)); ?>

    </div>

    <!-- Genel Bilgiler -->
    <ul class="list-group mb-3">
        <li class="list-group-item"><strong>Model:</strong> <?php echo e($equipment->model); ?></li>
        <li class="list-group-item"><strong>Seri No:</strong> <?php echo e($equipment->serial_no); ?></li>
        <li class="list-group-item"><strong>Durum:</strong> <?php echo e($equipment->status); ?></li>
        <li class="list-group-item"><strong>Kalibrasyon Tarihi:</strong> <?php echo e($equipment->calibration_due_at); ?></li>
    </ul>

    <!-- Bakım Kayıtları -->
    <div class="card mb-4">
        <div class="card-header">🔧 Bakım Kayıtları</div>
        <div class="card-body">
            <table class="table table-sm table-bordered">
                <thead><tr><th>Tarih</th><th>Tür</th><th>Sonuç</th></tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $equipment->maintenances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr><td><?php echo e($m->performed_at); ?></td><td><?php echo e($m->type); ?></td><td><?php echo e($m->result); ?></td></tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="3" class="text-center">Kayıt yok</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Kalibrasyonlar -->
    <div class="card mb-4">
        <div class="card-header">📏 Kalibrasyonlar</div>
        <div class="card-body">
            <table class="table table-sm table-bordered">
                <thead><tr><th>Tarih</th><th>Sertifika No</th><th>Sağlayıcı</th><th>Geçerlilik</th><th>PDF Sertifika</th></tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $equipment->calibrations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($c->performed_at); ?></td>
                            <td><?php echo e($c->certificate_no); ?></td>
                            <td><?php echo e($c->provider); ?></td>
                            <td><?php echo e($c->valid_until); ?></td>
                            <td>
                                <?php if($c->certificate_file): ?>
                                    <a href="<?php echo e(asset('storage/'.$c->certificate_file)); ?>" target="_blank" class="btn btn-sm btn-outline-primary">Görüntüle</a>
                                <?php else: ?>
                                    <span class="text-muted">Yok</span>
                                <?php endif; ?>
                                <!-- Güncelleme Formu -->
                                <form action="<?php echo e(route('equipment.calibration.update', [$equipment->id, $c->id])); ?>" 
                                      method="POST" enctype="multipart/form-data" class="mt-1">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                    <input type="file" name="certificate_file" class="form-control form-control-sm" accept="application/pdf">
                                    <button type="submit" class="btn btn-sm btn-warning mt-1">Güncelle</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="text-center">Kayıt yok</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- İç Kalite Kontrol -->
    <div class="card mb-4">
        <div class="card-header">🧪 İç Kalite Kontrol (IQC)</div>
        <div class="card-body">
            <table class="table table-sm table-bordered">
                <thead><tr><th>Tarih</th><th>Materyal</th><th>Sonuç</th><th>Kural</th></tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $equipment->iqcRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iqc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($iqc->date); ?></td>
                            <td><?php echo e($iqc->material); ?></td>
                            <td><?php echo e($iqc->value); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e($iqc->rule_status == 'OK' ? 'success' : 'danger'); ?>">
                                    <?php echo e($iqc->rule_status); ?>

                                </span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="text-center">Kayıt yok</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Dış Kalite Kontrol -->
    <div class="card mb-4">
        <div class="card-header">🌍 Dış Kalite Kontrol (EQC/PT)</div>
        <div class="card-body">
            <table class="table table-sm table-bordered">
                <thead><tr><th>Program</th><th>Round</th><th>Sonuç</th><th>Z-Score</th></tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $equipment->eqcRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eqc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr><td><?php echo e($eqc->provider); ?></td><td><?php echo e($eqc->round_no); ?></td><td><?php echo e($eqc->result); ?></td><td><?php echo e($eqc->z_score); ?></td></tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="text-center">Kayıt yok</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Dinamik Kayıt Formları -->
    <div class="card mb-4">
        <div class="card-header">➕ Yeni Kayıt Ekle</div>
        <div class="card-body">
            <div class="mb-3">
                <label>Kayıt Türü</label>
                <select id="recordType" class="form-select">
                    <option value="">Seçiniz...</option>
                    <option value="maintenance">Bakım</option>
                    <option value="calibration">Kalibrasyon</option>
                    <option value="iqc">IQC</option>
                    <option value="eqc">EQC</option>
                </select>
            </div>

            <!-- Bakım Formu -->
            <form id="maintenanceForm" action="<?php echo e(route('equipment.maintenance.store',$equipment->id)); ?>" method="POST" style="display:none;">
                <?php echo csrf_field(); ?>
                <div class="mb-2"><label>Tür</label><input type="text" name="type" class="form-control" required></div>
                <div class="mb-2"><label>Tarih</label><input type="date" name="performed_at" class="form-control" required></div>
                <div class="mb-2"><label>Sonuç</label><input type="text" name="result" class="form-control"></div>
                <button type="submit" class="btn btn-success">Kaydet</button>
            </form>

            <!-- Kalibrasyon Formu -->
            <form id="calibrationForm" action="<?php echo e(route('equipment.calibration.store',$equipment->id)); ?>" method="POST" enctype="multipart/form-data" style="display:none;">
                <?php echo csrf_field(); ?>
                <div class="mb-2"><label>Sertifika No</label><input type="text" name="certificate_no" class="form-control" required></div>
                <div class="mb-2"><label>Sağlayıcı</label><input type="text" name="provider" class="form-control" required></div>
                <div class="mb-2"><label>Tarih</label><input type="date" name="performed_at" class="form-control" required></div>
                <div class="mb-2"><label>Geçerlilik</label><input type="date" name="valid_until" class="form-control" required></div>
                <div class="mb-2"><label>Sertifika PDF</label><input type="file" name="certificate_file" class="form-control" accept="application/pdf"></div>
                <button type="submit" class="btn btn-success">Kaydet</button>
            </form>

            <!-- IQC Formu -->
            <form id="iqcForm" action="<?php echo e(route('equipment.iqc.store',$equipment->id)); ?>" method="POST" style="display:none;">
                <?php echo csrf_field(); ?>
                <div class="mb-2"><label>Tarih</label><input type="date" name="date" class="form-control" required></div>
                <div class="mb-2"><label>Materyal</label><input type="text" name="material" class="form-control" required></div>
                <div class="mb-2"><label>Sonuç</label><input type="text" name="value" class="form-control" required></div>
                <div class="mb-2"><label>Kural</label>
                    <select name="rule_status" class="form-select">
                        <option value="OK">OK</option>
                        <option value="Fail">Fail</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Kaydet</button>
            </form>

            <!-- EQC Formu -->
            <form id="eqcForm" action="<?php echo e(route('equipment.eqc.store',$equipment->id)); ?>" method="POST" style="display:none;">
                <?php echo csrf_field(); ?>
                <div class="mb-2"><label>Program</label><input type="text" name="provider" class="form-control" required></div>
                <div class="mb-2"><label>Round</label><input type="text" name="round_no" class="form-control" required></div>
                <div class="mb-2"><label>Sonuç</label><input type="text" name="result" class="form-control" required></div>
                <div class="mb-2"><label>Z-Score</label><input type="text" name="z_score" class="form-control"></div>
                <div class="mb-2"><label>Aksiyon Planı</label><textarea name="action_plan" class="form-control"></textarea></div>
                <button type="submit" class="btn btn-success">Kaydet</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('recordType').addEventListener('change', function() {
    // tüm formları gizle
    document.querySelectorAll('#maintenanceForm,#calibrationForm,#iqcForm,#eqcForm')
        .forEach(f => f.style.display='none');
    // seçilen formu göster
    if(this.value) {
        document.getElementById(this.value+'Form').style.display='block';
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/equipment/show.blade.php ENDPATH**/ ?>