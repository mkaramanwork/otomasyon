
<?php $__env->startSection('title','Sınav Yönetimi'); ?>
<?php $__env->startSection('content'); ?>
<div class="container">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>📘 Sınav Listesi</h4>
    <div class="ms-auto">
        <a href="<?php echo e(route('questions.index')); ?>" class="btn btn-warning me-2">
            <i class="fas fa-list"></i> Soru Listesi
        </a>
        <a href="<?php echo e(route('questions.create')); ?>" class="btn btn-danger me-2">
            <i class="fas fa-question"></i> Soru Ekle
        </a>
        <a href="<?php echo e(route('exams.create')); ?>" class="btn btn-success">
            <i class="fas fa-plus"></i> Yeni Sınav Oluştur
        </a>
    </div>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<!-- ✅ Kritik Sınavlar -->
<?php if($criticalExams->count()): ?>
<div class="card mb-4">
    <div class="card-header bg-danger text-white">⚠️ Çözülmesi Gereken Kritik Sınavlar</div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Başlık</th>
                    <th>Kategori</th>
                    <th>Soru Sayısı</th>
                    <th>Durum</th>
                    <th>Çöz</th>
                    <th>Detay</th>
                    <th>İşlemler</th>
                    <th>QR Kod</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $criticalExams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($exam->title); ?></td>
                    <td><?php echo e($exam->category->name); ?></td>
                    <td><?php echo e($exam->question_count); ?></td>
                    <td><span class="badge bg-danger">Çözülmedi</span></td>
                    <td><a href="<?php echo e(route('exams.solve',$exam->id)); ?>" class="btn btn-info btn-sm">Çöz</a></td>
                    <td><a href="<?php echo e(route('exams.show',$exam->id)); ?>" class="btn btn-primary btn-sm">Göster</a></td> <!-- ✅ -->
                    <td>
                        <a href="<?php echo e(route('exams.edit',$exam->id)); ?>" class="btn btn-warning btn-sm">Güncelle</a>
                        <form action="<?php echo e(route('exams.destroy',$exam->id)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Silmek istediğine emin misin?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                        </form>
                    </td>
                    <td><?php echo QrCode::size(80)->generate(route('exams.solve',$exam->id)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- ✅ Genel Sınav Listesi -->
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Başlık</th>
            <th>Kategori</th>
            <th>Soru Sayısı</th>
            <th>Durum</th>
            <th>Sonuç</th>
            <th>Detay</th>
            <th>İşlemler</th>
            <th>QR Kod</th>
        </tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $exams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
            <td><?php echo e($exam->title); ?></td>
            <td><?php echo e($exam->category->name); ?></td>
            <td><?php echo e($exam->question_count); ?></td>
            <td>
                <?php if($exam->assignments->where('completed',0)->count()): ?>
                    <span class="badge bg-warning">Beklemede</span>
                <?php else: ?>
                    <span class="badge bg-success">Tamamlandı</span>
                <?php endif; ?>
            </td>
            <td><a href="<?php echo e(route('exams.results',$exam->id)); ?>" class="btn btn-info btn-sm">Sonuç</a></td>
            <td><a href="<?php echo e(route('exams.show',$exam->id)); ?>" class="btn btn-primary btn-sm">Göster</a></td> <!-- ✅ -->
            <td>
                <a href="<?php echo e(route('exams.edit',$exam->id)); ?>" class="btn btn-warning btn-sm">Güncelle</a>
                <form action="<?php echo e(route('exams.destroy',$exam->id)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Silmek istediğine emin misin?')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                </form>
            </td>
            <td><?php echo QrCode::size(80)->generate(route('exams.solve',$exam->id)); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
            <td colspan="8" class="text-center">Henüz sınav tanımlı değil</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/exams/index.blade.php ENDPATH**/ ?>