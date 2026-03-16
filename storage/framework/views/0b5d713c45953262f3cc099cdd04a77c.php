

<?php $__env->startSection('title', 'Dolum Raporu'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
  
<form method="GET" class="row g-3 mb-4">
  <div class="col-md-3">
    <label for="start_date" class="form-label">Başlangıç Tarihi</label>
    <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo e(request('start_date')); ?>">
  </div>
  <div class="col-md-3">
    <label for="end_date" class="form-label">Bitiş Tarihi</label>
    <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo e(request('end_date')); ?>">
  </div>
  <div class="col-md-3">
    <label for="batch" class="form-label">Batch Numarası</label>
    <input type="text" name="batch" id="batch" class="form-control" value="<?php echo e(request('batch')); ?>" placeholder="örn: BATCH-2025">
  </div>
  <div class="col-md-3 align-self-end">
    <button type="submit" class="btn btn-primary">Filtrele</button>
    <a href="<?php echo e(route('reports.fillings')); ?>" class="btn btn-outline-secondary">Temizle</a>
  </div>
</form>


  
  <h4 class="mb-4">Dolum Raporu :          <tr>
          <td colspan="8" class="text-end fw-bold">Toplam Dolum:</td>
          <td class="fw-bold"><?php echo e(number_format($fillings->sum('total_kg'), 2)); ?> kg</td>
          <td colspan="4"></td>
        </tr></h4>

  <?php if($fillings->isEmpty()): ?>
    <div class="alert alert-info">Henüz dolum kaydı bulunmamaktadır.</div>
  <?php else: ?>
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Batch</th>
          <th>Ürün</th>
          <th>Dolum Hattı</th>
          <th>Reçete</th>
          <th>Adet</th>
          <th>Hacim (L)</th>
          <th>Yoğunluk</th>
          <th>Toplam (kg)</th>
          <th>Durum</th>
          <th>Tarih</th>
          <th>Detay</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $fillings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filling): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td><?php echo e($loop->iteration); ?></td>
            <td><?php echo e($filling->productionOrder->batch_number); ?></td>
            <td><?php echo e($filling->productionOrder->product->name ?? '-'); ?></td>
            <td><?php echo e($filling->fillingLine->name ?? '-'); ?></td>
            <td><?php echo e($filling->physicalFormula->product_name ?? '-'); ?></td>
            <td><?php echo e($filling->filled_quantity); ?></td>
            <td><?php echo e($filling->volume); ?></td>
            <td><?php echo e($filling->density); ?></td>
            <td><strong><?php echo e($filling->total_kg); ?> kg</strong></td>
            <td>
              <span class="badge bg-<?php echo e($filling->statusColor()); ?>">
                <?php echo e(ucfirst(str_replace('_', ' ', $filling->status))); ?>

              </span>
            </td>
            <td><?php echo e($filling->created_at->format('d.m.Y H:i')); ?></td>
            <td>
              <div class="d-flex flex-wrap gap-1">

              <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#actualModal-<?php echo e($filling->id); ?>">📦</button>
              <button class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#chemicalModal-<?php echo e($filling->id); ?>">🔬</button>
              <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#logModal-<?php echo e($filling->id); ?>">📜</button>
              </div>

            </td>
          </tr>

          
          <tr>
            <td colspan="12">
              <div class="modal fade" id="plannedModal-<?php echo e($filling->id); ?>" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">🧪 Fiziksel Reçete – Batch: <?php echo e($filling->productionOrder->batch_number); ?></h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <table class="table table-sm table-bordered">
                        <thead><tr><th>Parametre</th><th>Etiket</th><th>Yüzde (%)</th></tr></thead>
                        <tbody>
                          <?php $__currentLoopData = $filling->physicalFormula->details ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                              <td><?php echo e($detail->parameter->name ?? '-'); ?></td>
                              <td><?php echo e($detail->selection_label ?? '-'); ?></td>
                              <td><?php echo e($detail->quantity); ?>%</td>
                            </tr>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </td>
          </tr>

          
          <tr>
            <td colspan="12">
              <div class="modal fade" id="actualModal-<?php echo e($filling->id); ?>" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">📦 Gerçek Kullanım – Batch: <?php echo e($filling->productionOrder->batch_number); ?></h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <table class="table table-sm table-bordered">
                        <thead><tr><th>Parametre</th><th>Etiket</th><th>Yüzde (%)</th><th>Miktar (kg)</th></tr></thead>
                        <tbody>
                          <?php $__currentLoopData = $filling->calculated_materials ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                              <td><?php echo e($material['parameter']); ?></td>
                              <td><?php echo e($material['label']); ?></td>
                              <td><?php echo e($material['percentage']); ?>%</td>
                              <td><?php echo e($material['calculated_quantity']); ?> kg</td>
                            </tr>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </td>
          </tr>

          
          <tr>
            <td colspan="12">
              <div class="modal fade" id="chemicalModal-<?php echo e($filling->id); ?>" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">🔬 Kimyasal Reçete – <?php echo e($filling->productionOrder->product->name); ?></h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <table class="table table-sm table-bordered">
                        <thead><tr><th>Ham Madde</th><th>Yüzde (%)</th><th>Hesaplanan Miktar (kg)</th><th>Birim</th></tr></thead>
                        <tbody>
                          <?php $__currentLoopData = $filling->productionOrder->formula->details ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $kg = ($detail->percentage / 100) * $filling->total_kg; ?>
                            <tr>
                              <td><?php echo e($detail->rawMaterial->name ?? '-'); ?></td>
                              <td><?php echo e(number_format($detail->percentage, 2)); ?></td>
                              <td><?php echo e(number_format($kg, 2)); ?></td>
                              <td><?php echo e($detail->unit ?? '-'); ?></td>
                            </tr>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </td>
          </tr>

          
          <tr>
            <td colspan="12">
              <div class="modal fade" id="logModal-<?php echo e($filling->id); ?>" tabindex="-1">
                <div class="modal-dialog <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">📜 Üretim Logları – Batch: <?php echo e($filling->productionOrder->batch_number); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <?php if($filling->productionOrder->logs->isNotEmpty()): ?>
                      <ul class="list-group">
                        <?php $__currentLoopData = $filling->productionOrder->logs->sortByDesc('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <li class="list-group-item">
                            <strong><?php echo e($log->event); ?></strong> – <?php echo e($log->description); ?><br>
                            <small>
                              Log Zamanı: <?php echo e(optional($log->created_at)->format('d.m.Y H:i')); ?> |
                              Kullanıcı: <?php echo e($log->user->name ?? 'Sistem'); ?>

                            </small>
                          </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </ul>
                    <?php else: ?>
                      <div class="alert alert-warning mb-0">Bu batch için log kaydı bulunamadı.</div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="8" class="text-end fw-bold">Toplam Dolum:</td>
          <td class="fw-bold"><?php echo e(number_format($fillings->sum('total_kg'), 2)); ?> kg</td>
          <td colspan="4"></td>
        </tr>
      </tfoot>
    </table>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/reports/fillings.blade.php ENDPATH**/ ?>