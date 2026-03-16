
<?php $__env->startSection('title','Sınav Detayı'); ?>
<?php $__env->startSection('content'); ?>
<div class="container">

    <!-- ✅ Sınav Bilgileri -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">📘 <?php echo e($exam->title); ?> Detayı</div>
        <div class="card-body">
            <p><strong>Kategori:</strong> <?php echo e($exam->category->name); ?></p>
            <p><strong>Soru Sayısı:</strong> <?php echo e($exam->questions->count()); ?></p>
        </div>
    </div>

    <!-- ✅ Kullanıcı Atama Formu -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">👥 Kullanıcıya Atama</div>
        <div class="card-body">
            <form action="<?php echo e(route('exams.assign',$exam->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label>Kullanıcı Seç</label>
                    <select name="user_ids[]" id="userSelect" class="form-select" multiple required>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <small class="text-muted">Birden fazla kullanıcı seçebilirsin.</small>
                </div>
                <button type="submit" class="btn btn-success">Ata</button>
            </form>
        </div>
    </div>

    <!-- ✅ Mevcut Atamalar -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">📋 Mevcut Atamalar</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kullanıcı</th>
                        <th>Durum</th>
                        <th>Atama Tarihi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $exam->assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($assign->user->name); ?></td>
                        <td>
                            <?php if($assign->completed): ?>
                                <span class="badge bg-success">Tamamlandı</span>
                            <?php else: ?>
                                <span class="badge bg-warning">Beklemede</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($assign->assigned_at); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="3" class="text-center">Henüz atama yok</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ✅ Seçilen Sorular -->
    <div class="card">
        <div class="card-header bg-primary text-white">📝 Seçilen Sorular</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Soru Metni</th>
                        <th>Kategori</th>
                        <th>Zorluk</th>
                        <th>Doğru Cevap</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $exam->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($index+1); ?></td>
                        <td><?php echo e($q->question_text); ?></td>
                        <td><?php echo e($q->category->name); ?></td>
                        <td><?php echo e($q->difficulty); ?></td>
                        <td><?php echo e($q->correct_option); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="text-center">Bu sınava henüz soru eklenmemiş</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ✅ Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#userSelect').select2({
        placeholder: "Kullanıcı seçiniz",
        allowClear: true,
        width: '100%'
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/exams/show.blade.php ENDPATH**/ ?>