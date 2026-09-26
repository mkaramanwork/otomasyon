
<?php $__env->startSection('title','Teklif Detay'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4 class="mb-3">📄 Teklif Detay - <?php echo e($offer->offer_no); ?></h4>

    <!-- Genel Bilgiler -->
    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Firma:</strong> <?php echo e($offer->customer->name); ?></p>
            <p><strong>Başlık:</strong> <?php echo e($offer->title); ?></p>
            <p><strong>Metin:</strong> <?php echo e($offer->text); ?></p>
            <p><strong>Tarih:</strong> <?php echo e($offer->offer_date); ?></p>
            <p><strong>Durum:</strong> <?php echo e(ucfirst($offer->status)); ?></p>
            <p><strong>Toplam:</strong> <?php echo e(number_format($offer->total_amount,2)); ?> ₺</p>
            <p><strong>İndirim:</strong> %<?php echo e($offer->discount_rate); ?></p>
            <p><strong>Net:</strong> <?php echo e(number_format($offer->net_amount,2)); ?> ₺</p>
            <p><strong>Kalan Numune Hakkı:</strong> <?php echo e($offer->sample_count); ?></p>
        </div>
    </div>

    <!-- Parametreler -->
    <h5>Parametreler</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Parametre</th>
                <th>Metot</th>
                <th>Fiyat (₺)</th>
                <th>Kapsam</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $offer->parameters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $param): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($param->parameter->parameter); ?></td>
                <td><?php echo e($param->method); ?></td>
                <td><?php echo e(number_format($param->price,2)); ?></td>
                <td><?php echo e($param->scope); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <!-- Onaylı Teklif PDF -->
    <h5 class="mt-4">Onaylı Teklif PDF</h5>
    <?php if($offer->approved_pdf): ?>
        <a href="<?php echo e(asset('storage/'.$offer->approved_pdf)); ?>" target="_blank" class="btn btn-outline-primary">
            📄 PDF İndir
        </a>
    <?php else: ?>
        <form action="<?php echo e(route('offers.uploadPdf',$offer->id)); ?>" method="POST" enctype="multipart/form-data" class="mt-2">
            <?php echo csrf_field(); ?>
            <input type="file" name="approved_pdf" class="form-control mb-2" accept="application/pdf" required>
            <button class="btn btn-primary">PDF Yükle</button>
        </form>
    <?php endif; ?>

    <!-- Accordion -->
    <div class="accordion mt-4" id="samplesAccordion">

        <!-- Mevcut Numuneler -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingActive">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseActive">
                    📦 Mevcut Numuneler
                </button>
            </h2>
            <div id="collapseActive" class="accordion-collapse collapse show" data-bs-parent="#samplesAccordion">
                <div class="accordion-body">
                    <ul>
                        <?php $__currentLoopData = $samples; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sample): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <?php echo e($sample->batch_no); ?> - <?php echo e($sample->company_name); ?>

                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#sampleModal<?php echo e($sample->id); ?>">
                                    Detay
                                </button>
                            </li>

                            <!-- Modal -->
                            <div class="modal fade" id="sampleModal<?php echo e($sample->id); ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Numune Detayı - <?php echo e($sample->batch_no); ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Firma:</strong> <?php echo e($sample->company_name); ?></p>
                                            <p><strong>Ülke:</strong> <?php echo e($sample->country); ?></p>
                                            <p><strong>Alınma Tarihi:</strong> <?php echo e($sample->taken_at); ?></p>
                                            <p><strong>Miktar:</strong> <?php echo e($sample->amount); ?></p>
                                            <p><strong>Açıklama:</strong> <?php echo e($sample->description); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Silinmiş Numuneler -->
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingDeleted">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDeleted">
                    ❌ Silinmiş Numuneler
                </button>
            </h2>
            <div id="collapseDeleted" class="accordion-collapse collapse" data-bs-parent="#samplesAccordion">
                <div class="accordion-body">
                    <ul>
                        <?php $__currentLoopData = $deletedSamples; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sample): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <?php echo e($sample->batch_no); ?> - <?php echo e($sample->company_name); ?>

                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deletedSampleModal<?php echo e($sample->id); ?>">
                                    Detay
                                </button>
                            </li>

                            <!-- Modal -->
                            <div class="modal fade" id="deletedSampleModal<?php echo e($sample->id); ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Silinmiş Numune - <?php echo e($sample->batch_no); ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Firma:</strong> <?php echo e($sample->company_name); ?></p>
                                            <p><strong>Ülke:</strong> <?php echo e($sample->country); ?></p>
                                            <p><strong>Alınma Tarihi:</strong> <?php echo e($sample->taken_at); ?></p>
                                            <p><strong>Miktar:</strong> <?php echo e($sample->amount); ?></p>
                                            <p><strong>Açıklama:</strong> <?php echo e($sample->description); ?></p>
                                            <p class="text-danger"><strong>Durum:</strong> Bu numune silinmiş</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        </div>

    </div>

    <!-- Numune Bağlama Formu -->
    <form action="<?php echo e(route('offers.attachSample',$offer->id)); ?>" method="POST" class="mt-3">
        <?php echo csrf_field(); ?>
        <label for="sample_id" class="form-label">Numune Seç</label>
        <select name="sample_id" class="form-control mb-2" required>
            <option value="">Numune seçiniz</option>
            <?php $__currentLoopData = $samples; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sample): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(!$sample->trashed()): ?>
                    <option value="<?php echo e($sample->id); ?>"><?php echo e($sample->batch_no); ?> - <?php echo e($sample->company_name); ?></option>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <button class="btn btn-success">Numune Bağla</button>
    </form>

    <!-- Tümünü Bağla Butonu -->
    <form action="<?php echo e(route('offers.attachAllSamples',$offer->id)); ?>" method="POST" class="mt-3">
        <?php echo csrf_field(); ?>
        <button class="btn btn-warning">Tüm Numuneleri Bu Teklife Bağla</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/offers/show.blade.php ENDPATH**/ ?>