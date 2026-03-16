
<?php $__env->startSection('title','Sınav Sonuçları'); ?>
<?php $__env->startSection('content'); ?>
<div class="container">

    <!-- ✅ Sınav Bilgileri -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">📘 <?php echo e($exam->title); ?> Sonuçları</div>
        <div class="card-body">
            <p><strong>Kategori:</strong> <?php echo e($exam->category->name); ?></p>
            <p><strong>Soru Sayısı:</strong> <?php echo e($exam->questions->count()); ?></p>
        </div>
    </div>

    <!-- ✅ Kullanıcı Sonuçları -->
    <div class="card">
        <div class="card-header bg-primary text-white">📝 Cevaplarım</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Soru</th>
                        <th>Seçilen Cevap</th>
                        <th>Doğru Cevap</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $assignment->answers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $answer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($index+1); ?></td>
                        <td><?php echo e($answer->question->question_text); ?></td>
                        <td><?php echo e($answer->selected_option); ?></td>
                        <td><?php echo e($answer->question->correct_option); ?></td>
                        <td>
                            <?php if($answer->is_correct): ?>
                                <span class="badge bg-success">Doğru</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Yanlış</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

            <!-- ✅ Puan Hesabı -->
            <?php
                $total = $assignment->answers->count();
                $correct = $assignment->answers->where('is_correct',true)->count();
                $score = $total > 0 ? round(($correct/$total)*100) : 0;
            ?>

            <div class="alert alert-info mt-3">
                Toplam: <?php echo e($total); ?> soru<br>
                Doğru: <?php echo e($correct); ?><br>
                Puan: <strong><?php echo e($score); ?></strong>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/exams/results.blade.php ENDPATH**/ ?>