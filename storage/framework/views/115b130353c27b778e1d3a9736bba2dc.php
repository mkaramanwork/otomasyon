
<?php $__env->startSection('title','Cihaz Yönetimi'); ?>
<?php $__env->startSection('content'); ?>
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>🔧 Cihaz Listesi</h4>
        <a href="<?php echo e(route('equipment.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Cihaz Ekle
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <!-- ✅ Kritik Kalibrasyon Tablosu -->
    <?php if($criticalEquipments->count()): ?>
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">⚠️ Kalibrasyon Süresi 30 Gün Altında Olan Cihazlar</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kod</th>
                        <th>Adı</th>
                        <th>Model</th>
                        <th>Seri No</th>
                        <th>Kalan Gün</th>
                        <th>Detay</th>
                        <th>İşlemler</th>
                        <th>QR Kod</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $criticalEquipments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($eq->tag); ?></td>
                        <td><?php echo e($eq->name); ?></td>
                        <td><?php echo e($eq->model); ?></td>
                        <td><?php echo e($eq->serial_no); ?></td>
                        <td>
                            <span class="badge bg-danger"><?php echo e(intval($eq->days_left)); ?> gün</span>
                            <small class="text-muted"> (<?php echo e($eq->calibration_valid_until); ?>)</small>
                        </td>
                        <td><a href="<?php echo e(route('equipment.show',$eq->id)); ?>" class="btn btn-info btn-sm">Detay</a></td>
                        <td>
                            <a href="<?php echo e(route('equipment.edit',$eq->id)); ?>" class="btn btn-warning btn-sm">Güncelle</a>
                            <form action="<?php echo e(route('equipment.destroy',$eq->id)); ?>" method="POST" style="display:inline;" 
                                  onsubmit="return confirm('Bu cihazı silmek istediğine emin misin?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                            </form>
                        </td>
                        <td>
                            <?php echo QrCode::size(80)->generate(route('equipment.show',$eq->id)); ?>

                            <a href="<?php echo e(route('equipment.show',$eq->id)); ?>" download class="btn btn-sm btn-outline-secondary mt-1">İndir</a>
                            <?php if($eq->calibrations->last()?->certificate_file): ?>
                                <a href="<?php echo e(asset('storage/'.$eq->calibrations->last()->certificate_file)); ?>" 
                                   target="_blank" class="btn btn-sm btn-outline-primary mt-1">PDF Sertifika</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- ✅ Genel Cihaz Listesi -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Kod</th>
                <th>Adı</th>
                <th>Model</th>
                <th>Seri No</th>
                <th>Durum</th>
                <th>Kalan Kalibrasyon</th>
                <th>Detay</th>
                <th>İşlemler</th>
                <th>QR Kod</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $equipments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($eq->tag); ?></td>
                <td><?php echo e($eq->name); ?></td>
                <td><?php echo e($eq->model); ?></td>
                <td><?php echo e($eq->serial_no); ?></td>
                <td><?php echo e($eq->status); ?></td>
                <td>
                    <?php if($eq->days_left !== null): ?>
                        <?php if($eq->days_left > 60): ?>
                            <span class="badge bg-success"><?php echo e(intval($eq->days_left)); ?> gün</span>
                        <?php elseif($eq->days_left > 30): ?>
                            <span class="badge bg-warning"><?php echo e(intval($eq->days_left)); ?> gün</span>
                        <?php else: ?>
                            <span class="badge bg-danger"><?php echo e(intval($eq->days_left)); ?> gün</span>
                        <?php endif; ?>
                        <small class="text-muted"> (<?php echo e($eq->calibration_valid_until); ?>)</small>
                    <?php else: ?>
                        <span class="text-muted">Kalibrasyon kaydı yok</span>
                    <?php endif; ?>
                </td>
                <td><a href="<?php echo e(route('equipment.show',$eq->id)); ?>" class="btn btn-info btn-sm">Detay</a></td>
                <td>
                    <a href="<?php echo e(route('equipment.edit',$eq->id)); ?>" class="btn btn-warning btn-sm">Güncelle</a>
                    <form action="<?php echo e(route('equipment.destroy',$eq->id)); ?>" method="POST" style="display:inline;" 
                          onsubmit="return confirm('Bu cihazı silmek istediğine emin misin?')">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                    </form>
                </td>
                <td>
                    <?php echo QrCode::size(80)->generate(route('equipment.show',$eq->id)); ?>

                    <a href="<?php echo e(route('equipment.show',$eq->id)); ?>" download class="btn btn-sm btn-outline-secondary mt-1">İndir</a>
                    <?php if($eq->calibrations->last()?->certificate_file): ?>
                        <a href="<?php echo e(asset('storage/'.$eq->calibrations->last()->certificate_file)); ?>" 
                           target="_blank" class="btn btn-sm btn-outline-primary mt-1">PDF Sertifika</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="9" class="text-center">Henüz cihaz tanımlı değil</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/equipment/index.blade.php ENDPATH**/ ?>