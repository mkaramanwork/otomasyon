


<?php $__env->startSection('title', 'Üretim Takip Paneli'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
  <h2 class="text-center my-4">Üretim Durumları</h2>

  <table class="table table-bordered table-striped table-hover text-center">
    <thead class="table-dark">
      <tr>
        <th>Batch No</th>
        <th>Ürün</th>
        <th>Müşteri</th>
        <th>Üretim Hattı</th>
        <th>Dolum Hattı</th>
        <th>Dolum Miktarı</th>
        <th>Plan</th>
        <th>Miktar</th>
        <th>Durum</th>
        <th>Güncelleme</th>
      </tr>
    </thead>
    <tbody>
      <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td><?php echo e($order->batch_number ?? 'Tanımsız'); ?></td>
          <td><?php echo e($order->product->name); ?></td>
          <td><?php echo e($order->customer->name); ?></td>
          <td><?php echo e($order->productionLine->name); ?></td>
          <td>
            <?php if($order->status == 'in_filling' && $order->filling && $order->filling->fillingLine): ?>
              <?php echo e($order->filling->fillingLine->name); ?>

            <?php else: ?>
              -
            <?php endif; ?>
          </td>
          <td>
            <?php if($order->status == 'in_filling' && $order->filling): ?>
              <?php echo e($order->filling->total_kg); ?> kg
            <?php else: ?>
              -
            <?php endif; ?>
          </td>
          <td><?php echo e(\Carbon\Carbon::parse($order->planned_at)->format('d.m.Y H:i')); ?></td>
          <td><?php echo e($order->quantity_kg); ?> kg</td>
          <td>
            <?php if($order->status == 'planned'): ?>
              <span class="badge bg-secondary">Planlandı</span>
            <?php elseif($order->status == 'in_production'): ?>
              <span class="badge bg-warning text-dark">Üretimde</span>
            <?php elseif($order->status == 'in_filling'): ?>
              <span class="badge bg-info text-dark">Dolumda</span>
            <?php elseif($order->status == 'produced'): ?>
              <span class="badge bg-success">Tamamlandı</span>
            <?php elseif($order->status == 'filled'): ?>
              <span class="badge bg-primary">Dolum Tamamlandı</span>
            <?php elseif($order->status == 'cancelled'): ?>
              <span class="badge bg-danger">İptal</span>
            <?php endif; ?>
          </td>
          <td><?php echo e($order->updated_at->diffForHumans()); ?></td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
  </table>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.tv', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/tv/show.blade.php ENDPATH**/ ?>