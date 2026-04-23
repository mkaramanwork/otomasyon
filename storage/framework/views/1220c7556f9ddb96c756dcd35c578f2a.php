

<?php $__env->startSection('title', 'Numune Kabul'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>🧪 Numune Kabul Listesi</h4>
        <a href="<?php echo e(route('samples.accept-manual-create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Numune Kaydet
        </a>
    </div>

    
    <div class="mb-3">
        <input type="text" id="sampleSearch" class="form-control form-control-lg"
               placeholder="Ürün, firma, batch, talep no, numune türü, alan kişi...">
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
                                <td><?php echo e($sample->request_no); ?></td>
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
    <?php if($acceptedSamples->isEmpty()): ?>
        <div class="alert alert-info">Henüz kabul edilen numune yok.</div>
    <?php else: ?>
        <div class="card card-outline card-success shadow-sm">
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
                        <?php $__currentLoopData = $acceptedSamples; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sample): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($loop->iteration); ?></td>
                                <td><?php echo e($sample->labProduct->product_name ?? '-'); ?></td>
                                <td><?php echo e($sample->company_name); ?></td>
                                <td><?php echo e($sample->batch_no); ?></td>
                                <td><?php echo e($sample->request_no); ?></td>
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
    document.getElementById('sampleSearch').addEventListener('keyup', function () {
        let value = this.value.toLowerCase();
        let rows = document.querySelectorAll('.sampleTable tbody tr');
        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
        });
    });
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/sample_acceptances/index.blade.php ENDPATH**/ ?>