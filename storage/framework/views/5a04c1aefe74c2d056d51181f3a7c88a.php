

<?php $__env->startSection('title', 'Sipariş & Teklifler'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
  <h4>📑 Sipariş & Teklif Listesi</h4>
  <div>
    <a href="<?php echo e(route('orders.create')); ?>" class="btn btn-success">
      <i class="fas fa-plus"></i> Yeni Sipariş/Teklif
    </a>

  </div>
</div>


  <?php if(session('success')): ?>
    <div class="alert alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?>

  <?php if($orders->isEmpty()): ?>
    <div class="alert alert-info">Henüz sipariş veya teklif bulunamadı.</div>
  <?php else: ?>
    <div class="card card-outline card-primary">
      <div class="card-body p-0">
        <table class="table table-bordered table-striped align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Müşteri</th>
              <th>Ekleyen</th>
              <th>Tip</th>
              <th>Durum</th>
              <th>İlerleme</th> <!-- yeni sütun -->
              <th>Oluşturulma</th>
              <th class="text-center">İşlem</th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr>
                <td><?php echo e($loop->iteration); ?></td>
                <td><?php echo e($order->customer_name); ?></td>
                <td><?php echo e($order->user->name ?? '-'); ?></td>
                <td><?php echo e(ucfirst($order->type)); ?></td>
                <td><?php echo e(ucfirst($order->status)); ?></td>
                <td>
                  <?php echo e($order->items->where('status','tamamlandı')->count()); ?> / <?php echo e($order->items->count()); ?>

                </td>
                <td><?php echo e($order->created_at->format('d.m.Y H:i')); ?></td>
                <td class="text-center">
                  <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#orderModal<?php echo e($order->id); ?>">
                    <i class="fas fa-info-circle"></i> Detay
                  </button>
                  <a href="<?php echo e(route('orders.edit', $order->id)); ?>" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-edit"></i> Düzenle
                  </a>
                  <form action="<?php echo e(route('orders.destroy', $order->id)); ?>" method="POST" class="d-inline"
                        onsubmit="return confirm('Bu kaydı silmek istediğinize emin misiniz?')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                      <i class="fas fa-trash"></i> Sil
                    </button>
                  </form>
                </td>
              </tr>

          <!-- Modal -->
          <div class="modal fade" id="orderModal<?php echo e($order->id); ?>" tabindex="-1"
              aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Sipariş Detayı - <?php echo e($order->customer_name); ?></h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <p><strong>Müşteri:</strong> <?php echo e($order->customer_name); ?></p>
                  <p><strong>Email:</strong> <?php echo e($order->customer_email); ?></p>
                  <p><strong>Tip:</strong> <?php echo e(ucfirst($order->type)); ?></p>
                  <p><strong>Durum:</strong> <?php echo e(ucfirst($order->status)); ?></p>
                  <p><strong>Ekleyen:</strong> <?php echo e($order->user->name ?? '-'); ?></p>
                  <p><strong>Oluşturulma:</strong> <?php echo e($order->created_at->format('d.m.Y H:i')); ?></p>

                  <hr>
                  <h6>Ürünler</h6>
                  <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="row mb-2">
                      <div class="col-md-4"><?php echo e($item->product->name); ?></div>
                      <div class="col-md-2"><?php echo e($item->quantity); ?> adet</div>
                      <div class="col-md-2">Durum: <?php echo e(ucfirst($item->status)); ?></div>
                      <div class="col-md-2">
                        <?php if($item->status == 'beklemede'): ?>
                          <form action="<?php echo e(route('orders.items.complete',$item->id)); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-sm btn-warning">Üretildi</button>
                          </form>
                        <?php else: ?>
                          <span class="badge bg-success">Tamamlandı</span>
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                  <hr>
                  <h6>Toplam Bilgiler</h6>
                  <p><strong>Genel İskonto:</strong> <?php echo e($order->global_discount ?? 0); ?> %</p>
                  <p><strong>Toplam TL:</strong> <?php echo e(number_format($order->total_price,2)); ?> ₺</p>
                  <p><strong>Toplam USD:</strong> <?php echo e(number_format($order->total_price_usd,2)); ?> $</p>
                </div>
                <div class="modal-footer">
                  <a href="<?php echo e(route('orders.export', $order->id)); ?>" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Excel Aktar
                  </a>
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
              </div>
            </div>
          </div>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
      <div class="card-footer text-muted">
        Toplam Kayıt: <?php echo e($orders->count()); ?>

      </div>
    </div>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/orders/index.blade.php ENDPATH**/ ?>