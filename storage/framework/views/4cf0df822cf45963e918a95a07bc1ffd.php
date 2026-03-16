
<?php $__env->startSection('title','Sınav Çöz'); ?>
<?php $__env->startSection('content'); ?>
<div class="container">

    <!-- ✅ Sınav Bilgileri -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">📘 <?php echo e($exam->title); ?> Çözüm</div>
        <div class="card-body">
            <p><strong>Kategori:</strong> <?php echo e($exam->category->name); ?></p>
            <p><strong>Soru Sayısı:</strong> <?php echo e($exam->questions->count()); ?></p>
        </div>
    </div>

    <!-- ✅ Sınav Soruları -->
    <div class="card">
        <div class="card-header bg-primary text-white">📝 Sorular</div>
        <div class="card-body">
            <form action="<?php echo e(route('exams.submit',$exam->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <?php $__currentLoopData = $exam->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="mb-4">
                    <h5><?php echo e($index+1); ?>. <?php echo e($q->question_text); ?></h5>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answers[<?php echo e($q->id); ?>]" value="A" required>
                        <label class="form-check-label">A) <?php echo e($q->option_a); ?></label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answers[<?php echo e($q->id); ?>]" value="B">
                        <label class="form-check-label">B) <?php echo e($q->option_b); ?></label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answers[<?php echo e($q->id); ?>]" value="C">
                        <label class="form-check-label">C) <?php echo e($q->option_c); ?></label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answers[<?php echo e($q->id); ?>]" value="D">
                        <label class="form-check-label">D) <?php echo e($q->option_d); ?></label>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <button type="submit" class="btn btn-success">Sınavı Bitir</button>
            </form>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/exams/solve.blade.php ENDPATH**/ ?>