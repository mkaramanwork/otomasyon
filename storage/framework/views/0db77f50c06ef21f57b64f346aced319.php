
<?php $__env->startSection('title','Yeni Soru'); ?>
<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">📝 Yeni Soru</div>
        <div class="card-body">
            <form action="<?php echo e(route('questions.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label>Soru Metni</label>
                    <textarea name="question_text" class="form-control" required></textarea>
                </div>
                <div class="mb-3"><label>Seçenek A</label><input type="text" name="option_a" class="form-control" required></div>
                <div class="mb-3"><label>Seçenek B</label><input type="text" name="option_b" class="form-control" required></div>
                <div class="mb-3"><label>Seçenek C</label><input type="text" name="option_c" class="form-control" required></div>
                <div class="mb-3"><label>Seçenek D</label><input type="text" name="option_d" class="form-control" required></div>
                <div class="mb-3"><label>Doğru Cevap (A/B/C/D)</label><input type="text" name="correct_option" class="form-control" required></div>
                <div class="mb-3">
                    <label>Zorluk</label>
                    <select name="difficulty" class="form-select" required>
                        <option value="Kolay">Kolay</option>
                        <option value="Orta">Orta</option>
                        <option value="Zor">Zor</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Kategori</label>
                    <select name="category_id" class="form-select" required>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat->id); ?>"><?php echo e($cat->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Kaydet</button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/questions/create.blade.php ENDPATH**/ ?>