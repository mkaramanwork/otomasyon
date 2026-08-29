

<?php $__env->startSection('title','Yeni TDS'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">

    <h4>📄 Yeni TDS Oluştur</h4>

    <form action="<?php echo e(route('tds.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <div class="row g-3">

            <div class="col-md-4">
                <label>Ürün</label>
                <select name="product_id" class="form-select" required>
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="col-md-4">
                <label>Ürün Grubu</label>
                <select name="product_group" class="form-select" required>
                    <option value="Binek Araç">Binek Araç</option>
                    <option value="Ağır Hizmet">Ağır Hizmet</option>
                    <option value="Endüstriyel">Endüstriyel</option>
                    <option value="Antifriz">Antifriz</option>
                    <option value="Gres">Gres</option>
                    <option value="AdBlue">AdBlue</option>
                </select>
            </div>

            <div class="col-md-4">
                <label>Ürün Kodu</label>
                <input type="text" name="product_code" class="form-control">
            </div>

            <div class="col-md-6">
                <label>Ürün Adı</label>
                <input type="text" name="product_name" class="form-control">
            </div>

            <div class="col-md-12">
                <label>Açıklama</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>

            <div class="col-md-12 mt-3">
                <label>Onaylar</label>
                <textarea name="approvals" class="form-control" rows="2" placeholder="Örn: OEM Approval, ISO 9001"></textarea>
            </div>

            
            <div class="col-md-12">
                <label>Özellikler</label>

                <div id="featureContainer">
                    <div class="row mb-2 feature-row">
                        <div class="col-md-5">
                            <input type="text" name="features[0][title]" class="form-control" placeholder="Özellik Adı">
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="features[0][value]" class="form-control" placeholder="Değer">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger removeFeature">Sil</button>
                        </div>
                    </div>
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
                                <input type="checkbox" name="param_select[<?php echo e($p->id); ?>]">
                            </td>
                            <td><?php echo e($p->parameter); ?></td>
                            <td><?php echo e($p->unit); ?></td>
                            <td><?php echo e($p->method_tr); ?></td>
                            <td>
                                <input type="text" name="param_value[<?php echo e($p->id); ?>]" class="form-control"
                                       placeholder="Örn: 32 cSt">
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

        </div>

        <button class="btn btn-primary mt-3">Kaydet</button>
    </form>

</div>

<script>
let index = 1;

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
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/tds/create.blade.php ENDPATH**/ ?>