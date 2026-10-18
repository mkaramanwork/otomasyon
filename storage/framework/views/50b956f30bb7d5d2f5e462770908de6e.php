

<?php $__env->startSection('title','TDS Listesi'); ?>

<?php $__env->startSection('content'); ?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <h4 class="mb-0">
                📑 TDS Listesi
            </h4>

            <small class="text-muted">
                Teknik Bilgi Formları
            </small>

        </div>


        <div class="d-flex gap-2 flex-wrap">

            <a href="<?php echo e(route('tds.create')); ?>"
               class="btn btn-success">

                ➕ Yeni TDS

            </a>


            <a href="<?php echo e(route('tds.clone')); ?>"
               class="btn btn-dark">

                📄 TDS Klonla

            </a>


            <button type="button"
                    class="btn btn-dark"
                    data-bs-toggle="modal"
                    data-bs-target="#brandModal">

                🏷️ Marka Yönetimi

            </button>


            <a href="<?php echo e(route('tds.printAll')); ?>"
               class="btn btn-primary">

                🖨️ Tüm TDS’leri Yazdır

            </a>

        </div>

    </div>


    <?php if(session('success')): ?>

        <div class="alert alert-success alert-dismissible fade show">

            <?php echo e(session('success')); ?>


            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    <?php endif; ?>


    <?php if(session('error')): ?>

        <div class="alert alert-danger alert-dismissible fade show">

            <?php echo e(session('error')); ?>


            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    <?php endif; ?>


    <?php if($errors->any()): ?>

        <div class="alert alert-danger alert-dismissible fade show">

            <strong>
                İşlem gerçekleştirilemedi.
            </strong>

            <ul class="mb-0 mt-2">

                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <li>
                        <?php echo e($error); ?>

                    </li>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </ul>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    <?php endif; ?>


    <div class="card mb-3">

        <div class="card-header">

            <strong>
                🔎 TDS Ara
            </strong>

        </div>


        <div class="card-body">

            <div class="row g-2">

                <div class="col-md-9">

                    <div class="input-group">

                        <span class="input-group-text">
                            🔎
                        </span>

                        <input type="text"
                               id="tdsSearch"
                               class="form-control"
                               placeholder="Marka, ürün adı, ürün kodu veya açıklama ara...">

                    </div>

                </div>


                <div class="col-md-3">

                    <button type="button"
                            id="clearSearch"
                            class="btn btn-outline-secondary w-100">

                        Temizle

                    </button>

                </div>

            </div>


            <div class="mt-2">

                <small class="text-muted">

                    Görünen kayıt:

                    <strong id="visibleCount">
                        <?php echo e($documents->count()); ?>

                    </strong>

                    /

                    <?php echo e($documents->count()); ?>


                </small>

            </div>

        </div>

    </div>


    <div class="card">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-striped table-hover table-bordered mb-0"
                       id="tdsTable">

                    <thead class="table-dark">

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                Marka
                            </th>

                            <th>
                                Ürün Adı
                            </th>

                            <th>
                                Ürün Kodu
                            </th>

                            <th>
                                Açıklama
                            </th>

                            <th>
                                Oluşturulma
                            </th>

                            <th style="min-width: 420px;">
                                İşlemler
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php $__empty_1 = true; $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                            <tr class="tds-row">

                                <td>
                                    <?php echo e($doc->id); ?>

                                </td>


                                <td>

                                    <?php if($doc->brand): ?>

                                        <span class="badge text-bg-dark">

                                            <?php echo e($doc->brand->name); ?>


                                        </span>

                                    <?php else: ?>

                                        <span class="badge text-bg-secondary">

                                            Markasız

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <?php echo e($doc->product_name); ?>


                                </td>


                                <td>

                                    <?php echo e($doc->product_code); ?>


                                </td>


                                <td>

                                    <?php echo e(Str::limit($doc->description, 70)); ?>


                                </td>


                                <td>

                                    <?php echo e($doc->created_at?->format('d.m.Y H:i')); ?>


                                </td>


                                <td>

                                    <div class="d-flex gap-1 flex-wrap">

                                        <a href="<?php echo e(route('tds.show',$doc->id)); ?>"
                                           class="btn btn-info btn-sm">

                                            🔍 Detay

                                        </a>


                                        <a href="<?php echo e(route('tds.edit',$doc->id)); ?>"
                                           class="btn btn-warning btn-sm">

                                            ✏️ Düzenle

                                        </a>


                                        <a href="<?php echo e(route('tds.exportWord',$doc->id)); ?>"
                                           class="btn btn-primary btn-sm">

                                            📥
                                            <?php echo e($doc->brand?->name ?? 'TDS'); ?>

                                            Word

                                        </a>


                                        <form action="<?php echo e(route('tds.destroy',$doc->id)); ?>"
                                              method="POST">

                                            <?php echo csrf_field(); ?>

                                            <?php echo method_field('DELETE'); ?>

                                            <button type="submit"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Bu TDS kaydını silmek istediğine emin misin?')">

                                                🗑️ Sil

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                            <tr>

                                <td colspan="7"
                                    class="text-center text-muted py-4">

                                    Henüz TDS kaydı bulunmuyor.

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>



<div class="modal fade"
     id="brandModal"
     tabindex="-1"
     aria-labelledby="brandModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header bg-dark text-white">

                <h5 class="modal-title"
                    id="brandModalLabel">

                    🏷️ Marka Yönetimi

                </h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Kapat">
                </button>

            </div>


            <div class="modal-body">

                <form action="<?php echo e(route('brands.store')); ?>"
                      method="POST">

                    <?php echo csrf_field(); ?>


                    <label class="form-label fw-bold">

                        Yeni Marka Ekle

                    </label>


                    <div class="mb-3">

                        <label class="form-label">

                            Marka Adı

                        </label>

                        <input type="text"
                               name="name"
                               class="form-control"
                               placeholder="Örn: Murat, Karaman, MKWep"
                               value="<?php echo e(old('name')); ?>"
                               required>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">

                            TDS Taslak Dosyası

                        </label>

                        <div class="input-group">

                            <input type="text"
                                   name="template_file"
                                   class="form-control"
                                   placeholder="Örn: tds_mkwep.docx"
                                   value="<?php echo e(old('template_file')); ?>">

                            <button type="submit"
                                    class="btn btn-success">

                                ➕ Ekle

                            </button>

                        </div>

                        <small class="text-muted">

                            Word taslağının dosya adını .docx uzantısı ile yazınız.

                        </small>

                    </div>

                </form>


                <hr>


                <div class="d-flex justify-content-between align-items-center mb-2">

                    <h6 class="mb-0">

                        Mevcut Markalar

                    </h6>

                    <span class="badge text-bg-secondary">

                        <?php echo e($brands->count()); ?> Marka

                    </span>

                </div>


                <div style="max-height:350px; overflow-y:auto;">

                    <?php $__empty_1 = true; $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                        <div class="d-flex
                                    justify-content-between
                                    align-items-center
                                    border
                                    rounded
                                    p-2
                                    mb-2">

                            <div>

                                <strong>

                                    <?php echo e($brand->name); ?>


                                </strong>


                                <div class="mt-1">

                                    <?php if($brand->template_file): ?>

                                        <small class="text-muted">

                                            Taslak:

                                            <span class="badge text-bg-success">

                                                <?php echo e($brand->template_file); ?>


                                            </span>

                                        </small>

                                    <?php else: ?>

                                        <span class="badge text-bg-warning">

                                            Taslak Tanımlanmamış

                                        </span>

                                    <?php endif; ?>

                                </div>

                            </div>


                            <form action="<?php echo e(route('brands.destroy',$brand->id)); ?>"
                                  method="POST">

                                <?php echo csrf_field(); ?>

                                <?php echo method_field('DELETE'); ?>

                                <button type="submit"
                                        class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('<?php echo e($brand->name); ?> markasını silmek istediğine emin misin?')">

                                    🗑️ Sil

                                </button>

                            </form>

                        </div>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

                        <div class="alert alert-light border text-center">

                            Henüz marka eklenmemiş.

                        </div>

                    <?php endif; ?>

                </div>

            </div>


            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                    Kapat

                </button>

            </div>

        </div>

    </div>

</div>



<script>

document.addEventListener('DOMContentLoaded', function () {

    const searchInput =
        document.getElementById('tdsSearch');

    const clearButton =
        document.getElementById('clearSearch');

    const rows =
        document.querySelectorAll(
            '#tdsTable tbody .tds-row'
        );

    const visibleCount =
        document.getElementById('visibleCount');


    function filterTable() {

        const searchValue =
            searchInput.value
                .toLocaleLowerCase('tr-TR')
                .trim();

        let count = 0;


        rows.forEach(function (row) {

            const rowText =
                row.innerText
                    .toLocaleLowerCase('tr-TR');


            if (rowText.includes(searchValue)) {

                row.style.display = '';

                count++;

            } else {

                row.style.display = 'none';

            }

        });


        visibleCount.textContent = count;

    }


    searchInput.addEventListener(
        'input',
        filterTable
    );


    clearButton.addEventListener(
        'click',
        function () {

            searchInput.value = '';

            filterTable();

            searchInput.focus();

        }
    );

});

</script>


<?php if($errors->any()): ?>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const brandModalElement =
        document.getElementById('brandModal');


    if (
        brandModalElement &&
        typeof bootstrap !== 'undefined'
    ) {

        const brandModal =
            new bootstrap.Modal(
                brandModalElement
            );

        brandModal.show();

    }

});

</script>

<?php endif; ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/tds/index.blade.php ENDPATH**/ ?>