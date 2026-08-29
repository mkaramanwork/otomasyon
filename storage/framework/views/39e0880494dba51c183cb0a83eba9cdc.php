```php


<?php $__env->startSection('title','Manuel Numune Kabul'); ?>

<?php $__env->startSection('content'); ?>

<div class="container">

    <h4>📄 Manuel Numune Kabul (Yeni Kayıt)</h4>

    <form action="<?php echo e(route('samples.accept-manual-store')); ?>" method="POST">

        <?php echo csrf_field(); ?>

        <div class="row g-3">

            <div class="col-md-4">
                <label class="form-label">Ürün</label>

                <select name="product_id" id="labProductSelect" class="form-select" required>
                    <option value="">Ürün seçiniz...</option>

                    <?php $__currentLoopData = \App\Models\LabProduct::select('id', 'product_code', 'product_name')
                            ->orderBy('product_code')
                            ->get()
                            ->unique('product_code'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($lp->id); ?>">
                            <?php echo e($lp->product_code); ?> - <?php echo e($lp->product_name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Firma</label>

                <select name="customer_id" class="form-select" required>
                    <?php $__currentLoopData = \App\Models\Customer::orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($customer->id); ?>">
                            <?php echo e($customer->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Batch No</label>
                <input type="text" name="batch_no" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Ülke</label>
                <input type="text" name="country" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Belirsizlik</label>

                <select name="uncertainty" class="form-select" required>
                    <option value="0">Hayır</option>
                    <option value="1">Evet</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Rapor Talebi</label>

                <select name="report_request" class="form-select" required>
                    <option value="0">Hayır</option>
                    <option value="1">Evet</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Rapor No</label>
                <input type="text" name="report_no" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Miktar</label>

                <input
                    type="number"
                    name="amount"
                    class="form-control"
                    min="1"
                    value="300"
                >
            </div>

            <div class="col-md-4">
                <label class="form-label">Numune Türü</label>

                <select name="sample_type" class="form-select" required>
                    <option value="madeni yağ">Madeni Yağ</option>
                    <option value="antifriz">Antifriz</option>
                    <option value="gres">Gres</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Alındığı Yer</label>

                <select name="taken_place" class="form-select" required>
                    <option value="üretim hattı">Üretim Hattı</option>
                    <option value="ham madde">Ham Madde</option>
                    <option value="harmanlama">Harmanlama</option>
                    <option value="dolum numunesi">Dolum Numunesi</option>
                    <option value="müşteri">Müşteri</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Alan Kişi</label>
                <input type="text" name="sampling_person" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Alan Kişi Tipi</label>

                <select name="taken_by" class="form-select" required>
                    <option value="üretim çalışanı">Üretim Çalışanı</option>
                    <option value="müşteri">Müşteri</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Alınma Tarihi</label>

                <input
                    type="datetime-local"
                    name="taken_at"
                    class="form-control"
                    required
                >
            </div>

            <div class="col-md-4">
                <label class="form-label">Koruma</label>

                <select name="protection" class="form-select" required>
                    <option value="korumasız">Korumasız</option>
                    <option value="bilinmiyor">Bilinmiyor</option>
                    <option value="soğuk taşıma">Soğuk Taşıma</option>
                    <option value="mühürlü">Mühürlü</option>
                </select>
            </div>

            <div class="col-12">
                <label class="form-label">Açıklama</label>

                <textarea
                    name="description"
                    class="form-control"
                    rows="2"
                ></textarea>
            </div>

            <div class="col-12 mt-3">

                <label class="form-label">
                    Analiz Parametreleri
                </label>

                <input
                    type="text"
                    id="parameterSearch"
                    class="form-control mb-2"
                    placeholder="Numune türü veya parametreye göre ara..."
                >

                <table
                    class="table table-sm table-bordered"
                    id="parametersTable"
                >

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
                                    <input
                                        type="checkbox"
                                        name="parameters[]"
                                        value="<?php echo e($param->id); ?>"
                                    >
                                </td>

                                <td>
                                    <?php echo e($param->sample_type ?? '-'); ?>

                                </td>

                                <td>
                                    <?php echo e($param->parameter ?? '-'); ?>

                                </td>

                                <td>
                                    <?php echo e($param->method_tr ?? '-'); ?>

                                </td>

                                <td>
                                    <?php echo e($param->unit ?? '-'); ?>

                                </td>

                                <td>
                                    <?php echo e($param->scope ?? '-'); ?>

                                </td>

                            </tr>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </tbody>

                </table>

            </div>

        </div>

        <button
            type="submit"
            class="btn btn-primary mt-3"
        >
            Kaydet ve Kabul Et
        </button>

        <a
            href="<?php echo e(route('samples.index')); ?>"
            class="btn btn-secondary mt-3"
        >
            Geri Dön
        </a>

    </form>

</div>

<link
    href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
    rel="stylesheet"
/>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
.select2-container {
    width: 100% !important;
}

.select2-container .select2-selection--single {
    height: 38px !important;
    border: 1px solid #ced4da !important;
    border-radius: 0.375rem !important;
}

.select2-container .select2-selection--single .select2-selection__rendered {
    line-height: 36px !important;
    padding-left: 12px !important;
}

.select2-container .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
}

.select2-dropdown {
    z-index: 9999 !important;
}

.select2-search--dropdown .select2-search__field {
    width: 100% !important;
    padding: 8px !important;
}
</style>

<script>
$(document).ready(function () {

    $('#labProductSelect').select2({
        placeholder: 'Ürün kodu veya adı yazınız...',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function () {
                return 'Ürün bulunamadı';
            },
            searching: function () {
                return 'Aranıyor...';
            }
        }
    });

    $('#parameterSearch').on('keyup', function () {

        let searchValue = $(this).val().toLowerCase().trim();

        $('#parametersTable tbody tr').each(function () {

            let sampleType = $(this).find('td:eq(1)').text().toLowerCase();
            let parameter = $(this).find('td:eq(2)').text().toLowerCase();
            let method = $(this).find('td:eq(3)').text().toLowerCase();
            let unit = $(this).find('td:eq(4)').text().toLowerCase();

            if (
                sampleType.includes(searchValue) ||
                parameter.includes(searchValue) ||
                method.includes(searchValue) ||
                unit.includes(searchValue)
            ) {
                $(this).show();
            } else {
                $(this).hide();
            }

        });

    });

});
</script>

<?php $__env->stopSection(); ?>
```

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/sample_acceptances/accept_manual.blade.php ENDPATH**/ ?>