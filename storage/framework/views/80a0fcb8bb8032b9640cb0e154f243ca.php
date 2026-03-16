
<?php $__env->startSection('title','Sınav Güncelle'); ?>
<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="card">
        <div class="card-header bg-warning text-dark">✏️ Sınav Güncelle</div>
        <div class="card-body">
            <form action="<?php echo e(route('exams.update',$exam->id)); ?>" method="POST">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="mb-3">
                    <label>Başlık</label>
                    <input type="text" name="title" class="form-control" value="<?php echo e($exam->title); ?>" required>
                </div>
                <div class="mb-3">
                    <label>Kategori</label>
                    <select id="categorySelect" name="category_id" class="form-select" required>
                        <option value="">-- Seçiniz --</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat->id); ?>" <?php if($exam->category_id==$cat->id): ?> selected <?php endif; ?>>
                                <?php echo e($cat->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Soru Sayısı (otomatik)</label>
                    <input type="number" id="questionCount" name="question_count" class="form-control" value="<?php echo e($exam->question_count); ?>" readonly>
                </div>

                <!-- ✅ Soru Seçim Alanı -->
                <div class="mb-3">
                    <label>Sorular</label>
                    <div class="table-responsive" style="max-height:300px; overflow-y:auto;">
                        <table class="table table-sm table-bordered" id="questionsTable">
                            <thead>
                                <tr>
                                    <th>Seç</th>
                                    <th>Soru</th>
                                    <th>Kategori</th>
                                    <th>Zorluk</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr data-category="<?php echo e($q->category_id); ?>">
                                    <td>
                                        <input type="checkbox" name="question_ids[]" value="<?php echo e($q->id); ?>" 
                                            class="question-checkbox"
                                            <?php if($exam->questions->contains($q->id)): ?> checked <?php endif; ?>>
                                    </td>
                                    <td><?php echo e($q->question_text); ?></td>
                                    <td><?php echo e($q->category->name); ?></td>
                                    <td><?php echo e($q->difficulty); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Güncelle</button>
            </form>
        </div>
    </div>
</div>

<!-- ✅ JS Mantığı -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.question-checkbox');
    const questionCountInput = document.getElementById('questionCount');
    const categorySelect = document.getElementById('categorySelect');
    const rows = document.querySelectorAll('#questionsTable tbody tr');

    // Başlangıçta seçili soruların sayısını yaz
    questionCountInput.value = document.querySelectorAll('.question-checkbox:checked').length;

    // Checkbox değiştikçe güncelle
    checkboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            const selected = document.querySelectorAll('.question-checkbox:checked').length;
            questionCountInput.value = selected;
        });
    });

    // Kategoriye göre filtreleme
    categorySelect.addEventListener('change', () => {
        const selectedCat = categorySelect.value;
        rows.forEach(row => {
            if (!selectedCat || row.dataset.category === selectedCat) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/exams/edit.blade.php ENDPATH**/ ?>