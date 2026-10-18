

<?php $__env->startSection('title', 'Numune Kabul'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>🧪 Numune Kabul Listesi</h4>
        <div>
            <a href="<?php echo e(route('samples.accept-manual-create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yeni Numune Kaydet
            </a>
            <a href="<?php echo e(route('samples.export-excel')); ?>" class="btn btn-success ms-2">
                <i class="fas fa-file-excel"></i> Numune Kabul Aktar (Excel)
            </a>
            <a href="<?php echo e(route('samples.export-excel-template')); ?>" class="btn btn-success ms-2">
                <i class="fas fa-file-excel"></i> Numune Kabul Aktar (Excel Template)
            </a>
                    </div>
    </div>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <!-- ✅ Analiz Bekleyen Numuneler -->
    <h5 class="mt-4">📥 Analiz Bekleyen Numuneler</h5>
    <?php if($pendingSamples->isEmpty()): ?>
        <div class="alert alert-info">Bekleyen numune bulunamadı.</div>
    <?php else: ?>
        <div class="card card-outline card-primary mb-4 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-bordered table-striped align-middle mb-0 sampleTable">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Ürün</th>
                            <th>Firma</th>
                            <th>Batch</th>
                            <th>Talep No</th>
                            <th>Numune Türü</th>
                            <th>Alındığı Yer</th>
                            <th>Alan Kişi</th>
                            <th>Alınma Tarihi</th>
                            <th class="text-center">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $pendingSamples; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sample): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($loop->iteration); ?></td>
                                <td><?php echo e($sample->labProduct->product_name ?? '-'); ?></td>
                                <td><?php echo e($sample->company_name); ?></td>
                                <td><?php echo e($sample->batch_no); ?></td>
                                <td><?php echo e($sample->report_no); ?></td>
                                <td><?php echo e($sample->sample_type); ?></td>
                                <td><?php echo e($sample->taken_place); ?></td>
                                <td><?php echo e($sample->taken_by); ?></td>
                                <td><?php echo e($sample->taken_at); ?></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="<?php echo e(route('samples.acceptForm',$sample->id)); ?>" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i> Kabul Et
                                        </a>
                                        <form action="<?php echo e(route('samples.reject', $sample->id)); ?>" method="POST" class="d-inline"
                                              onsubmit="return confirm('Bu numuneyi reddetmek istediğinize emin misiniz?')">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-times"></i> Reddet
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-muted">
                Toplam Bekleyen Numune: <?php echo e($pendingSamples->count()); ?>

            </div>
        </div>
    <?php endif; ?>

    <!-- ✅ Kabul Edilen Numuneler -->
    <h5 class="mt-4">✅ Kabul Edilen Numuneler</h5>
    <div class="mb-3">
        <label for="acceptedSampleSearch" class="form-label fw-semibold">Ürün Adı / Batch No ile Ara</label>
        <input type="search" id="acceptedSampleSearch" class="form-control"
               placeholder="Ürün adı veya Batch No yazınız..." autocomplete="off">
    </div>
    <?php if($acceptedSamples->isEmpty()): ?>
        <div class="alert alert-info">Henüz kabul edilen numune yok.</div>
    <?php else: ?>
        <div class="card card-outline card-success shadow-sm">
            <div class="card-body p-0">
                <table id="acceptedSamplesTable" class="table table-bordered table-striped align-middle mb-0 sampleTable">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Ürün</th>
                            <th>Firma</th>
                            <th>Batch</th>
                            <th>Talep No</th>
                            <th>Numune Türü</th>
                            <th>Alındığı Yer</th>
                            <th>Alan Kişi</th>
                            <th>Alınma Tarihi</th>
                            <th class="text-center">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $acceptedSamples; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sample): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($loop->iteration); ?></td>
                                <td><?php echo e($sample->labProduct->product_name ?? '-'); ?></td>
                                <td><?php echo e($sample->company_name); ?></td>
                                <td><?php echo e($sample->batch_no); ?></td>
                                <td><?php echo e($sample->report_no); ?></td>
                                <td><?php echo e($sample->sample_type); ?></td>
                                <td><?php echo e($sample->taken_place); ?></td>
                                <td><?php echo e($sample->taken_by); ?></td>
                                <td><?php echo e($sample->taken_at); ?></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="<?php echo e(route('samples.labels',$sample->id)); ?>" target="_blank" class="btn btn-sm btn-success">
                                            <i class="fas fa-tag"></i> Etiket
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info"
                                                data-bs-toggle="modal"
                                                data-bs-target="#sampleModal<?php echo e($sample->id); ?>">
                                            <i class="fas fa-eye"></i> Detay
                                        </button>
                                        <a href="<?php echo e(route('samples.edit',$sample->id)); ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Güncelle
                                        </a>
                                        <form action="<?php echo e(route('samples.destroy',$sample->id)); ?>" method="POST" class="d-inline"
                                              onsubmit="return confirm('Bu numuneyi silmek istediğinize emin misiniz?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> Sil
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-muted">
                Toplam Kabul Edilen Numune: <?php echo e($acceptedSamples->count()); ?>

            </div>
        </div>

        <!-- ✅ Detay Modalları -->
        <?php $__currentLoopData = $acceptedSamples; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sample): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="modal fade" id="sampleModal<?php echo e($sample->id); ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title">Numune Detay</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <ul class="list-group mb-3">
                                <li class="list-group-item"><strong>Firma:</strong> <?php echo e($sample->company_name); ?></li>
                                <li class="list-group-item"><strong>Batch No:</strong> <?php echo e($sample->batch_no); ?></li>
                                <li class="list-group-item"><strong>Talep No:</strong> <?php echo e($sample->request_no); ?></li>
                                <li class="list-group-item"><strong>Numune Türü:</strong> <?php echo e($sample->sample_type); ?></li>
                                <li class="list-group-item"><strong>Alındığı Yer:</strong> <?php echo e($sample->taken_place); ?></li>
                                <li class="list-group-item"><strong>Alan Kişi:</strong> <?php echo e($sample->taken_by); ?></li>
                                <li class="list-group-item"><strong>Alınma Tarihi:</strong> <?php echo e($sample->taken_at); ?></li>
                            </ul>

                            <h6>📊 Parametreler</h6>
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Numune Türü</th>
                                        <th>Parametre</th>
                                        <th>Metot (TR)</th>
                                        <th>Birim</th>
                                        <th>Kapsam</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $sample->parameters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $param): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($param->sample_type); ?></td>
                                            <td><?php echo e($param->parameter); ?></td>
                                            <td><?php echo e($param->method_tr); ?></td>
                                            <td><?php echo e($param->unit); ?></td>
                                            <td><?php echo e($param->scope); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                              </tbody>
                            </table>

                            
                            <hr class="my-4">
                            <h6 class="mb-3"><i class="fas fa-history me-1"></i> Numune İşlem ve Revizyon Geçmişi</h6>
                            <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                                <table class="table table-sm table-bordered table-striped align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="min-width: 145px;">İşlem Tarihi</th>
                                            <th style="min-width: 150px;">İşlem</th>
                                            <th style="min-width: 130px;">Kullanıcı</th>
                                            <th style="min-width: 300px;">Açıklama / Değişiklik</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $sampleLogsById->get($sample->id, collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                <td><?php echo e($log->created_at?->format('d.m.Y H:i:s') ?? '-'); ?></td>
                                                <td><?php echo e($log->action); ?></td>
                                                <td><?php echo e($log->user->name ?? 'Bilinmiyor'); ?></td>
                                                <td style="white-space: pre-line; overflow-wrap: anywhere;"><?php echo e($log->description); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">Bu numuneye ait işlem kaydı bulunamadı.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
</div>

<?php if(session('open_labels')): ?>
    <script>
        window.open("<?php echo e(session('open_labels')); ?>", "_blank");
    </script>
<?php endif; ?>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('acceptedSampleSearch');
        const table = document.getElementById('acceptedSamplesTable');
        if (!searchInput || !table) return;

        searchInput.addEventListener('input', function () {
            const search = this.value.trim().toLocaleLowerCase('tr-TR');
            table.querySelectorAll('tbody tr').forEach(function (row) {
                const product = (row.cells[1]?.textContent || '').toLocaleLowerCase('tr-TR');
                const batch = (row.cells[3]?.textContent || '').toLocaleLowerCase('tr-TR');
                row.style.display = (product.includes(search) || batch.includes(search)) ? '' : 'none';
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/sample_acceptances/index.blade.php ENDPATH**/ ?>