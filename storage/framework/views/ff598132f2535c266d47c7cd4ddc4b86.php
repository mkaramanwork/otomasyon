

<?php $__env->startSection('title','Müşteri Şikayet / Öneri Modülü'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mt-4">
    <h3>📑 Müşteri Şikayet / Öneri Modülü</h3>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="accordion" id="complaintAccordion">

        
        <div class="accordion-item mb-3">
            <h2 class="accordion-header" id="headingCreate">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCreate">
                    ✍️ Yeni Şikayet / Öneri Kaydı
                </button>
            </h2>
            <div id="collapseCreate" class="accordion-collapse collapse" data-bs-parent="#complaintAccordion">
                <div class="accordion-body">
                    <form action="<?php echo e(route('complaints.store')); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label>Şikayet Konusu</label>
                            <input type="text" name="subject" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Tanım</label>
                            <textarea name="description" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Sahibi (Firma/Kişi)</label>
                            <input type="text" name="owner" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Telefon</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Şikayeti Alan</label>
                            <input type="text" name="received_by" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Ürün Seçimi</label>
                            <select name="product_id" class="form-control">
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Dosya Ekle (Görsel/PDF)</label>
                            <input type="file" name="files[]" multiple class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </form>
                </div>
            </div>
        </div>

        
        <div class="accordion-item mb-3">
            <h2 class="accordion-header" id="headingList">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseList">
                    📋 Kayıtlı Şikayetler
                </button>
            </h2>
            <div id="collapseList" class="accordion-collapse collapse" data-bs-parent="#complaintAccordion">
                <div class="accordion-body">
                    <table class="table mt-3">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Konu</th>
                                <th>Ürün</th>
                                <th>Sahibi</th>
                                <th>Durum</th>
                                <th>Kayıt Eden</th>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $complaints; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($c->complaint_no); ?></td>
                                    <td><?php echo e($c->subject); ?></td>
                                    <td><?php echo e($c->product->name); ?></td>
                                    <td><?php echo e($c->owner); ?></td>
                                    <td><?php echo e($c->status); ?></td>
                                    <td><?php echo e($c->user->name); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('complaints.show',$c->id)); ?>" class="btn btn-info btn-sm">Detay</a>
                                        <a href="<?php echo e(route('complaints.edit',$c->id)); ?>" class="btn btn-warning btn-sm">Düzenle</a>
                                        <a href="<?php echo e(route('complaints.exportWord',$c->id)); ?>" class="btn btn-success btn-sm">Word</a>
                                        <form action="<?php echo e(route('complaints.destroy',$c->id)); ?>" method="POST" style="display:inline-block;" 
                                            onsubmit="return confirm('Bu şikayeti silmek istediğine emin misin?');">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                                        </form>
                                    </td>


                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/complaints/index.blade.php ENDPATH**/ ?>