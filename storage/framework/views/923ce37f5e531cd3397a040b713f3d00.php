

<?php $__env->startSection('title','Ürün Sicil Kartları'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mt-4">

    <!-- Başarılı mesaj -->
    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo session('success'); ?></div>
        <?php if(session('product_id')): ?>
            <script>
                setTimeout(function() {
                    window.location.href = "<?php echo e(route('urunAgaci.show', session('product_id'))); ?>";
                }, 1000);
            </script>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Geri Dön Butonu -->
<div class="mb-3">
    <a href="<?php echo e(route('urunAgaci.index')); ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Sicil Kartı Listesine Dön
    </a>

    <a href="<?php echo e(route('urunAgaci.sicilCard', $product->id)); ?>" class="btn btn-primary ms-2">
        <i class="fas fa-file-word"></i> Sicil Kartı Export
    </a>
</div>


    <h3>🔎 Ürün Sicil Kartı: <?php echo e($product->name); ?> (Kod: <?php echo e($product->code); ?>)</h3>

    <div class="accordion" id="urunAccordion">

        <!-- Kimyasal Formüller -->
        <div class="accordion-item mb-3">
            <h2 class="accordion-header" id="headingFormula">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFormula">
                    ⚗️ Kimyasal Formüller
                </button>
            </h2>
            <div id="collapseFormula" class="accordion-collapse collapse" data-bs-parent="#urunAccordion">
                <div class="accordion-body">
                    <a href="<?php echo e(route('formulas.create', ['product_id' => $product->id])); ?>" class="btn btn-success mb-3">➕ Formül Ekle</a>

                    <?php if($product->formulas->isEmpty()): ?>
                        <div class="alert alert-info">Henüz formül eklenmemiş.</div>
                    <?php else: ?>
                        <?php $__currentLoopData = $product->formulas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $formula): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="card mb-3">
                                <div class="card-header">
                                    <strong><?php echo e($index + 1); ?>. <?php echo e($formula->name); ?></strong>
                                    <span class="text-muted">(<?php echo e($formula->type); ?>)</span>
                                </div>
                                <div class="card-body">
                                    <p><?php echo e($formula->description ?? 'Açıklama yok'); ?></p>
                                    <p><small>Ekleyen: <?php echo e($formula->addedBy->name ?? '-'); ?> | Tarih: <?php echo e($formula->created_at->format('d.m.Y H:i')); ?></small></p>

                                    <?php if($formula->details->isEmpty()): ?>
                                        <div class="alert alert-warning">Bu formüle ait ham madde bulunamadı.</div>
                                        <a href="<?php echo e(route('formulas.edit', $formula->id)); ?>" class="btn btn-sm btn-primary">➕ Ham Madde Ekle</a>
                                    <?php else: ?>
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Ham Madde</th>
                                                    <th>Oran (%)</th>
                                                    <th>Birim</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $formula->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><?php echo e($detail->rawMaterial->name); ?></td>
                                                        <td><?php echo e($detail->percentage); ?></td>
                                                        <td><?php echo e($detail->unit ?? '-'); ?></td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                        <a href="<?php echo e(route('formulas.edit', $formula->id)); ?>" class="btn btn-sm btn-primary">➕ Yeni Ham Madde Ekle</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Fiziksel Reçeteler -->
        <div class="accordion-item mb-3">
            <h2 class="accordion-header" id="headingPhysical">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePhysical">
                    ⚙️ Fiziksel Reçeteler
                </button>
            </h2>
            <div id="collapsePhysical" class="accordion-collapse collapse" data-bs-parent="#urunAccordion">
                <div class="accordion-body">
                    <a href="<?php echo e(route('physcial_formula.create', ['product_id' => $product->id])); ?>" class="btn btn-success mb-3">➕ Fiziksel Reçete Ekle</a>

                    <?php if($product->physicalFormulas->isEmpty()): ?>
                        <div class="alert alert-info">Henüz fiziksel reçete eklenmemiş.</div>
                    <?php else: ?>
                        <?php $__currentLoopData = $product->physicalFormulas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $formula): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="card mb-3">
                                <div class="card-header">
                                    <strong><?php echo e($index + 1); ?>. <?php echo e($formula->product_name); ?></strong>
                                    <span class="text-muted">(<?php echo e($formula->unit ?? '-'); ?>)</span>
                                </div>
                                <div class="card-body">
                                    <p><?php echo e($formula->description ?? 'Açıklama yok'); ?></p>
                                    <p><small>Tarih: <?php echo e($formula->created_at->format('d.m.Y H:i')); ?></small></p>

                                    <?php if($formula->details->isEmpty()): ?>
                                        <div class="alert alert-warning">Bu reçeteye ait parametre bulunamadı.</div>
                                        <a href="<?php echo e(route('physcial_formula.addDetail', $formula->id)); ?>" class="btn btn-sm btn-primary">➕ Parametre Ekle</a>
                                    <?php else: ?>
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Parametre</th>
                                                    <th>Seçim</th>
                                                    <th>Miktar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $formula->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><?php echo e($detail->parameter->name); ?></td>
                                                        <td><?php echo e($detail->selection_label); ?></td>
                                                        <td><?php echo e($detail->quantity); ?></td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                        <a href="<?php echo e(route('physcial_formula.addDetail', $formula->id)); ?>" class="btn btn-sm btn-primary">➕ Yeni Parametre Ekle</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Barkodlar -->
        <div class="accordion-item mb-3">
            <h2 class="accordion-header" id="headingBarcode">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBarcode">
                    📦 Barkodlar
                </button>
            </h2>
            <div id="collapseBarcode" class="accordion-collapse collapse" data-bs-parent="#urunAccordion">
                <div class="accordion-body">

                    <!-- Barkod Ekle Butonu -->
                    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addBarcodeModal">
                        ➕ Barkod Ekle
                    </button>

                    <?php if($product->barcodes->isEmpty()): ?>
                        <div class="alert alert-info">Henüz barkod eklenmemiş.</div>
                    <?php else: ?>
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Barkod</th>
                                    <th>Açıklama</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $product->barcodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $barcode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($index + 1); ?></td>
                                        <td class="text-center">
                                            <span id="barcode-<?php echo e($barcode->id); ?>"><?php echo e($barcode->code); ?></span>
                                            <button class="btn btn-sm btn-outline-secondary ms-2" 
                                                    onclick="copyToClipboard('barcode-<?php echo e($barcode->id); ?>')">
                                                📋
                                            </button>
                                        </td>
                                        <td><?php echo e($barcode->description ?? '-'); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editBarcodeModal<?php echo e($barcode->id); ?>">
                                                Düzenle
                                            </button>

                                            <!-- Sil -->
                                            <form action="<?php echo e(route('barcodes.destroy', $barcode->id)); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Silmek istediğinize emin misiniz?')">
                                                    Sil
                                                </button>
                                            </form>

                                            <!-- İndir -->
                                            <a href="<?php echo e(route('barcodes.download', $barcode->id)); ?>" class="btn btn-sm btn-info">
                                                ⬇️ İndir
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- Düzenleme Modal -->
                                    <div class="modal fade" id="editBarcodeModal<?php echo e($barcode->id); ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form method="POST" action="<?php echo e(route('barcodes.update', $barcode->id)); ?>">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PUT'); ?>
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Barkod Düzenle</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Barkod Kodu</label>
                                                            <input type="text" name="code" class="form-control" value="<?php echo e($barcode->code); ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Açıklama</label>
                                                            <input type="text" name="description" class="form-control" value="<?php echo e($barcode->description); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-success">Kaydet</button>
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <!-- Barkod Ekle Modal -->
        <div class="modal fade" id="addBarcodeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="<?php echo e(route('barcodes.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">

                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Barkod Ekle</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Barkod Kodu</label>
                                <input type="text" name="code" class="form-control" placeholder="Örn: 8691234567890" required>
                                <small class="text-muted">⚠️ EAN-13 standardına göre barkod 13 haneli olmalıdır.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Açıklama</label>
                                <input type="text" name="description" class="form-control" placeholder="Avrupa barkodu, iç pazar barkodu...">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Kaydet</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        
<!-- Etiket Görselleri -->
<div class="accordion-item mb-3">
    <h2 class="accordion-header" id="headingLabels">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLabels">
            🏷️ Etiket Görselleri
        </button>
    </h2>
    <div id="collapseLabels" class="accordion-collapse collapse" data-bs-parent="#urunAccordion">
        <div class="accordion-body">
            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addLabelModal">➕ Etiket Görseli Ekle</button>

            <?php if($product->labels->isEmpty()): ?>
                <div class="alert alert-info">Henüz etiket görseli eklenmemiş.</div>
            <?php else: ?>
                <div class="row">
                    <?php $__currentLoopData = $product->labels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-3 text-center mb-3">
                            <?php if(Str::endsWith($label->file_path, ['.png','.jpg','.jpeg'])): ?>
                                <img src="<?php echo e(asset('storage/'.$label->file_path)); ?>" class="img-thumbnail" alt="Etiket">
                            <?php elseif(Str::endsWith($label->file_path, '.pdf')): ?>
                                <i class="bi bi-file-earmark-pdf" style="font-size:48px;color:red;"></i>
                            <?php endif; ?>

                            <p class="mt-2"><?php echo e($label->name); ?> (<?php echo e($label->type); ?>)</p>
                            <p><?php echo e($label->description ?? '-'); ?></p>

                            <a href="<?php echo e(route('labels.download', $label->id)); ?>" class="btn btn-sm btn-info">⬇️ İndir</a>
                            <form action="<?php echo e(route('labels.destroy', $label->id)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-danger">Sil</button>
                            </form>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Etiket Ekleme Modal -->
<div class="modal fade" id="addLabelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="<?php echo e(route('labels.store')); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Etiket Görseli Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Etiket Adı</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tip</label>
                        <select name="type" class="form-select" required>
                            <option value="ön">Ön</option>
                            <option value="arka">Arka</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dosya (PNG, JPG, JPEG, PDF)</label>
                        <input type="file" name="file" class="form-control" accept=".png,.jpg,.jpeg,.pdf" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Kaydet</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Koli Görselleri -->
<div class="accordion-item mb-3">
    <h2 class="accordion-header" id="headingBoxes">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBoxes">
            📦 Koli Görselleri
        </button>
    </h2>
    <div id="collapseBoxes" class="accordion-collapse collapse" data-bs-parent="#urunAccordion">
        <div class="accordion-body">
            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addBoxModal">➕ Koli Görseli Ekle</button>

            <?php if($product->packageBoxes->isEmpty()): ?>
                <div class="alert alert-info">Henüz koli görseli eklenmemiş.</div>
            <?php else: ?>
                <div class="row">
                    <?php $__currentLoopData = $product->packageBoxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $box): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-3 text-center mb-3">
                            <?php if(Str::endsWith($box->file_path, ['.png','.jpg','.jpeg'])): ?>
                                <img src="<?php echo e(asset('storage/'.$box->file_path)); ?>" class="img-thumbnail" alt="Koli">
                            <?php elseif(Str::endsWith($box->file_path, '.pdf')): ?>
                                <i class="bi bi-file-earmark-pdf" style="font-size:48px;color:red;"></i>
                            <?php endif; ?>

                            <p class="mt-2"><?php echo e($box->name); ?></p>
                            <p><?php echo e($box->description ?? '-'); ?></p>

                            <a href="<?php echo e(route('package-boxes.download', $box->id)); ?>" class="btn btn-sm btn-info">⬇️ İndir</a>
                            <form action="<?php echo e(route('package-boxes.destroy', $box->id)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-danger">Sil</button>
                            </form>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Koli Görseli Ekleme Modal -->
<div class="modal fade" id="addBoxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="<?php echo e(route('package-boxes.store')); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Koli Görseli Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Koli Adı</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dosya (PNG, JPG, JPEG, PDF)</label>
                        <input type="file" name="file" class="form-control" accept=".png,.jpg,.jpeg,.pdf" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Kaydet</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- Koli Görseli Ekleme Modal -->
<div class="accordion-item mb-3">
    <div class="modal fade" id="addBoxModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="<?php echo e(route('package-boxes.store')); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Koli Görseli Ekle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Koli Adı</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Açıklama</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dosya (PNG, JPG, JPEG, PDF)</label>
                            <input type="file" name="file" class="form-control" accept=".png,.jpg,.jpeg,.pdf" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Kaydet</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Kapaklar -->
<div class="accordion-item mb-3">
    <h2 class="accordion-header" id="headingCaps">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCaps">
            🧢 Kapaklar
        </button>
    </h2>
    <div id="collapseCaps" class="accordion-collapse collapse" data-bs-parent="#urunAccordion">
        <div class="accordion-body">
            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addCapModal">➕ Kapak Ekle</button>

            <?php if($product->caps->isEmpty()): ?>
                <div class="alert alert-info">Henüz kapak eklenmemiş.</div>
            <?php else: ?>
                <div class="row">
                    <?php $__currentLoopData = $product->caps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cap): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-3 text-center mb-3">
                            <?php if(Str::endsWith($cap->file_path, ['.png','.jpg','.jpeg'])): ?>
                                <img src="<?php echo e(asset('storage/'.$cap->file_path)); ?>" class="img-thumbnail" alt="Kapak">
                            <?php elseif(Str::endsWith($cap->file_path, '.pdf')): ?>
                                <i class="bi bi-file-earmark-pdf" style="font-size:48px;color:red;"></i>
                            <?php endif; ?>

                            <p class="mt-2"><?php echo e($cap->name); ?></p>
                            <p><?php echo e($cap->description ?? '-'); ?></p>

                            <a href="<?php echo e(route('caps.download', $cap->id)); ?>" class="btn btn-sm btn-info">⬇️ İndir</a>
                            <form action="<?php echo e(route('caps.destroy', $cap->id)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-danger">Sil</button>
                            </form>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Kapak Ekleme Modal -->
<div class="modal fade" id="addCapModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="<?php echo e(route('caps.store')); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kapak Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kapak Adı</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dosya (PNG, JPG, JPEG, PDF)</label>
                        <input type="file" name="file" class="form-control" accept=".png,.jpg,.jpeg,.pdf" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Kaydet</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Şişe / Ambalaj Görselleri -->
<div class="accordion-item mb-3">
    <h2 class="accordion-header" id="headingBottles">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBottles">
            🍶 Şişe / Ambalaj Görselleri
        </button>
    </h2>
    <div id="collapseBottles" class="accordion-collapse collapse" data-bs-parent="#urunAccordion">
        <div class="accordion-body">
            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addBottleModal">➕ Şişe / Ambalaj Ekle</button>

            <?php if($product->bottles->isEmpty()): ?>
                <div class="alert alert-info">Henüz şişe/ambalaj eklenmemiş.</div>
            <?php else: ?>
                <div class="row">
                    <?php $__currentLoopData = $product->bottles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bottle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-3 text-center mb-3">
                            <?php if(Str::endsWith($bottle->file_path, ['.png','.jpg','.jpeg'])): ?>
                                <img src="<?php echo e(asset('storage/'.$bottle->file_path)); ?>" class="img-thumbnail" alt="Şişe">
                            <?php elseif(Str::endsWith($bottle->file_path, '.pdf')): ?>
                                <i class="bi bi-file-earmark-pdf" style="font-size:48px;color:red;"></i>
                            <?php endif; ?>

                            <p class="mt-2"><?php echo e($bottle->name); ?></p>
                            <p><?php echo e($bottle->description ?? '-'); ?></p>

                            <a href="<?php echo e(route('bottles.download', $bottle->id)); ?>" class="btn btn-sm btn-info">⬇️ İndir</a>
                            <form action="<?php echo e(route('bottles.destroy', $bottle->id)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-danger">Sil</button>
                            </form>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Şişe / Ambalaj Ekleme Modal -->
<div class="modal fade" id="addBottleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="<?php echo e(route('bottles.store')); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Şişe / Ambalaj Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Şişe / Ambalaj Adı</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dosya (PNG, JPG, JPEG, PDF)</label>
                        <input type="file" name="file" class="form-control" accept=".png,.jpg,.jpeg,.pdf" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Kaydet</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- TDS Dokümanları -->
<div class="accordion-item mb-3">
    <h2 class="accordion-header" id="headingTdsDocs">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTdsDocs">
            📄 TDS Dokümanları
        </button>
    </h2>
    <div id="collapseTdsDocs" class="accordion-collapse collapse" data-bs-parent="#urunAccordion">
        <div class="accordion-body">

            <?php if($product->tdsDocuments->isEmpty()): ?>
                <div class="alert alert-warning">
                    Bu ürüne ait TDS dokümanı bulunamadı.<br>
                    Lütfen <a href="<?php echo e(route('tds.create')); ?>" class="alert-link">TDS ekleme modülünden</a> ekleyiniz.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Ürün Adı</th>
                                <th>Ürün Kodu</th>
                                <th>Açıklama</th>
                                <th>Onaylar</th>
                                <th>Özellikler</th>
                                <th>Parametreler</th>
                                <th>Word Export</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $product->tdsDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($doc->product_name); ?></td>
                                    <td><?php echo e($doc->product_code); ?></td>
                                    <td><?php echo e($doc->description); ?></td>
                                    <td><?php echo e($doc->approvals ?? '-'); ?></td>
                                    <td>
                                        <?php $__currentLoopData = $doc->features ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            • <?php echo e($f['title'] ?? ''); ?>: <?php echo e($f['value'] ?? ''); ?> <br>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                    <td>
                                        <?php $__currentLoopData = $doc->parameters ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php echo e($p['parameter']); ?> (<?php echo e($p['unit']); ?>): <?php echo e($p['value']); ?> <br>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('tds.exportWord',$doc->id)); ?>" class="btn btn-sm btn-primary">
                                            Word Export
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

</div> <!-- accordion end -->
<script>
function copyToClipboard(elementId) {
    var text = document.getElementById(elementId).innerText;
    navigator.clipboard.writeText(text).then(function() {
        alert("Barkod kopyalandı: " + text);
    });
}
</script>
</div> <!-- container end -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/urun_agaci/show.blade.php ENDPATH**/ ?>