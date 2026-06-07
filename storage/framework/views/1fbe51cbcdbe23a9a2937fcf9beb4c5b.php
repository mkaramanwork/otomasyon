

<?php $__env->startSection('title','Numune Güncelle'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>✏️ Numune Güncelle</h4>

    <form action="<?php echo e(route('samples.update',$sample->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="row g-3">

            <!-- Ürün -->
            <div class="col-md-4">
                <label class="form-label">Ürün</label>
                <select name="product_id" id="labProductSelect" class="form-select" required>
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($lp->id); ?>" <?php if($sample->product_id == $lp->id): ?> selected <?php endif; ?>>
                            <?php echo e($lp->product_code); ?> - <?php echo e($lp->product_name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <!-- Firma -->
            <div class="col-md-4">
                <label class="form-label">Firma</label>
                <input type="text" name="company_name" class="form-control" value="<?php echo e(old('company_name',$sample->company_name)); ?>">
            </div>

            <!-- Batch No -->
            <div class="col-md-4">
                <label class="form-label">Batch No</label>
                <input type="text" name="batch_no" class="form-control" value="<?php echo e(old('batch_no',$sample->batch_no)); ?>">
            </div>

            <!-- Talep No -->
            <div class="col-md-4">
                <label class="form-label">Talep No</label>
                <input type="text" name="request_no" class="form-control" value="<?php echo e(old('request_no',$sample->request_no)); ?>">
            </div>

            <!-- Numune Türü -->
            <div class="col-md-4">
                <label class="form-label">Numune Türü</label>
                <select name="sample_type" class="form-select">
                    <option value="madeni yağ" <?php if($sample->sample_type=="madeni yağ"): ?> selected <?php endif; ?>>Madeni Yağ</option>
                    <option value="antifriz" <?php if($sample->sample_type=="antifriz"): ?> selected <?php endif; ?>>Antifriz</option>
                    <option value="gres" <?php if($sample->sample_type=="gres"): ?> selected <?php endif; ?>>Gres</option>
                </select>
            </div>

            <!-- Alındığı Yer (ENUM) -->
            <div class="col-md-4">
                <label class="form-label">Alındığı Yer</label>
                <select name="taken_place" class="form-select">
                    <option value="üretim hattı" <?php if($sample->taken_place=="üretim hattı"): ?> selected <?php endif; ?>>Üretim Hattı</option>
                    <option value="ham madde" <?php if($sample->taken_place=="ham madde"): ?> selected <?php endif; ?>>Ham Madde</option>
                    <option value="harmanlama" <?php if($sample->taken_place=="harmanlama"): ?> selected <?php endif; ?>>Harmanlama</option>
                    <option value="dolum numunesi" <?php if($sample->taken_place=="dolum numunesi"): ?> selected <?php endif; ?>>Dolum Numunesi</option>
                    <option value="müşteri" <?php if($sample->taken_place=="müşteri"): ?> selected <?php endif; ?>>Müşteri</option>
                    <option value="kantar" <?php if($sample->taken_place=="kantar"): ?> selected <?php endif; ?>>Kantar</option>
                </select>
            </div>

            <!-- Alan Kişi -->
            <div class="col-md-4">
                <label class="form-label">Alan Kişi</label>
                <input type="text" name="sampling_person" class="form-control" value="<?php echo e(old('sampling_person',$sample->sampling_person)); ?>">
            </div>

            <!-- Alan Kişi Tipi (ENUM) -->
            <div class="col-md-4">
                <label class="form-label">Alan Kişi Tipi</label>
                <select name="taken_by" class="form-select">
                    <option value="üretim çalışanı" <?php if($sample->taken_by=="üretim çalışanı"): ?> selected <?php endif; ?>>Üretim Çalışanı</option>
                    <option value="müşteri" <?php if($sample->taken_by=="müşteri"): ?> selected <?php endif; ?>>Müşteri</option>
                    <option value="kantarsorumlusu" <?php if($sample->taken_by=="kantarsorumlusu"): ?> selected <?php endif; ?>>Kantar Sorumlusu</option>
                </select>
            </div>

            <!-- Alınma Tarihi -->
            <div class="col-md-4">
                <label class="form-label">Alınma Tarihi</label>
                <input type="datetime-local" name="taken_at" class="form-control"
                       value="<?php echo e(old('taken_at',$sample->taken_at ? $sample->taken_at->format('Y-m-d\TH:i') : '')); ?>">
            </div>

            <!-- Ülke -->
            <div class="col-md-4">
                <label class="form-label">Ülke</label>
                <input type="text" name="country" class="form-control" value="<?php echo e(old('country',$sample->country)); ?>">
            </div>

            <!-- Rapor No -->
            <div class="col-md-4">
                <label class="form-label">Rapor No</label>
                <input type="text" name="report_no" class="form-control" value="<?php echo e(old('report_no',$sample->report_no)); ?>">
            </div>

            <!-- Miktar -->
            <div class="col-md-4">
                <label class="form-label">Miktar</label>
                <input type="number" name="amount" class="form-control" value="<?php echo e(old('amount',$sample->amount)); ?>">
            </div>

            <!-- Koruma -->
            <div class="col-md-4">
                <label class="form-label">Koruma</label>
                <input type="text" name="protection" class="form-control" value="<?php echo e(old('protection',$sample->protection)); ?>">
            </div>

            <!-- Açıklama -->
            <div class="col-12">
                <label class="form-label">Açıklama</label>
                <textarea name="description" class="form-control" rows="2"><?php echo e(old('description',$sample->description)); ?></textarea>
            </div>

            <!-- Parametreler -->
            <div class="col-12 mt-3">
                <h5>📊 Parametreler</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Seç</th>
                            <th>Numune Türü</th>
                            <th>Parametre</th>
                            <th>Metot (TR)</th>
                            <th>Birim</th>
                            <th>Kapsam</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $parameters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $param): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="selected_parameters[]" value="<?php echo e($param->id); ?>"
                                           <?php if($sample->parameters->contains($param->id)): ?> checked <?php endif; ?>>
                                </td>
                                <td><?php echo e($param->sample_type); ?></td>
                                <td><?php echo e($param->parameter); ?></td>
                                <td><?php echo e($param->method_tr); ?></td>
                                <td><?php echo e($param->unit); ?></td>
                                <td><?php echo e($param->scope); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Güncelle
            </button>
            <a href="<?php echo e(route('samples.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Geri Dön
            </a>
        </div>
    </form>
</div>


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$('#labProductSelect').select2({
    placeholder: "Ürün seçiniz...",
    allowClear: true,
    width: '100%'
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/sample_acceptances/edit.blade.php ENDPATH**/ ?>