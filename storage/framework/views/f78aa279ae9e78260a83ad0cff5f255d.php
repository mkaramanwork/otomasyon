

<?php $__env->startSection('title','TDS Düzenle'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">

    <h4>✏️ TDS Düzenle</h4>

    <form action="<?php echo e(route('tds.update',$doc->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="row g-3">

            <div class="col-md-4">
                <label>Ürün</label>
                <select name="product_id" class="form-select" required>
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($p->id); ?>" <?php if($doc->product_id==$p->id): ?> selected <?php endif; ?>><?php echo e($p->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="col-md-4">
                <label>Ürün Grubu</label>
                <select name="product_group" class="form-select" required>
                    <option value="Binek Araç" <?php if($doc->product_group=="Binek Araç"): ?> selected <?php endif; ?>>Binek Araç</option>
                    <option value="Ağır Hizmet" <?php if($doc->product_group=="Ağır Hizmet"): ?> selected <?php endif; ?>>Ağır Hizmet</option>
                    <option value="Endüstriyel" <?php if($doc->product_group=="Endüstriyel"): ?> selected <?php endif; ?>>Endüstriyel</option>
                    <option value="Antifriz" <?php if($doc->product_group=="Antifriz"): ?> selected <?php endif; ?>>Antifriz</option>
                    <option value="Gres" <?php if($doc->product_group=="Gres"): ?> selected <?php endif; ?>>Gres</option>
                    <option value="AdBlue" <?php if($doc->product_group=="AdBlue"): ?> selected <?php endif; ?>>AdBlue</option>
                </select>
            </div>

            <div class="col-md-4">
                <label>Ürün Kodu</label>
                <input type="text" name="product_code" class="form-control" value="<?php echo e($doc->product_code); ?>">
            </div>

            <div class="col-md-6">
                <label>Ürün Adı</label>
                <input type="text" name="product_name" class="form-control" value="<?php echo e($doc->product_name); ?>">
            </div>

            <div class="col-md-12">
                <label>Açıklama</label>
                <textarea name="description" class="form-control" rows="3"><?php echo e($doc->description); ?></textarea>
            </div>

            <div class="col-md-12 mt-3">
                <label>Onaylar</label>
                <textarea name="approvals" class="form-control" rows="2"><?php echo e($doc->approvals); ?></textarea>
            </div>

            
            <div class="col-md-12">
                <label>Özellikler</label>

                <div id="featureContainer">
                    <?php $__currentLoopData = $doc->features ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="row mb-2 feature-row">
                        <div class="col-md-5">
                            <input type="text" name="features[<?php echo e($i); ?>][title]" class="form-control" value="<?php echo e($f['title'] ?? ''); ?>">
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="features[<?php echo e($i); ?>][value]" class="form-control" value="<?php echo e($f['value'] ?? ''); ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger removeFeature">Sil</button>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <button type="button" id="addFeature" class="btn btn-secondary mt-2">Özellik Ekle</button>
            </div>

            
            <div class="col-md-12 mt-4">
                <label>Parametreler</label>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Seç</th>
                            <th>Parametre</th>
                            <th>Birim</th>
                            <th>Metot</th>
                            <th>Değer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $params; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <input type="checkbox" name="param_select[<?php echo e($p->id); ?>]" 
                                    <?php if(isset($doc->parameters) && collect($doc->parameters)->pluck('id')->contains($p->id)): ?> checked <?php endif; ?>>
                            </td>
                            <td><?php echo e($p->parameter); ?></td>
                            <td><?php echo e($p->unit); ?></td>
                            <td><?php echo e($p->method_tr); ?></td>
                            <td>
                                <input type="text" name="param_value[<?php echo e($p->id); ?>]" class="form-control"
                                       value="<?php echo e(collect($doc->parameters)->firstWhere('id',$p->id)['value'] ?? ''); ?>">
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

        </div>

        <button class="btn btn-primary mt-3">Güncelle</button>
    </form>

</div>

<script>
let index = <?php echo e(count($doc->features ?? [])); ?>;

document.getElementById('addFeature').addEventListener('click', function () {
    let container = document.getElementById('featureContainer');
    let html = `
        <div class="row mb-2 feature-row">
            <div class="col-md-5">
                <input type="text" name="features[${index}][title]" class="form-control" placeholder="Özellik Adı">
            </div>
            <div class="col-md-5">
                <input type="text" name="features[${index}][value]" class="form-control" placeholder="Değer">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger removeFeature">Sil</button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    index++;
});

document.addEventListener('click', function(e){
    if(e.target.classList.contains('removeFeature')){
        e.target.closest('.feature-row').remove();
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/tds/edit.blade.php ENDPATH**/ ?>