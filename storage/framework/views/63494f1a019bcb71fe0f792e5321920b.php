

<?php $__env->startSection('title', 'Üretimdeki İş Emirleri'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
  <div class="col-md-12">
    <h4 class="mb-4">Aktif Üretim Süreci</h4>

    <?php if($orders->isEmpty()): ?>
      <div class="alert alert-info">Şu anda üretimde olan iş emri bulunmamaktadır.</div>
    <?php else: ?>
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>Batch</th>
            <th>Ürün</th>
            <th>Hat</th>
            <th>Planlanan</th>
            <th>Miktar</th>
            <th>Formül</th>
            <th>İşlem</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
              <td><?php echo e($order->batch_number); ?></td>
              <td><?php echo e($order->product->name); ?></td>
              <td><?php echo e($order->line->name); ?></td>
              <td><?php echo e(\Carbon\Carbon::parse($order->planned_at)->format('d.m.Y H:i')); ?></td>
              <td><?php echo e(number_format($order->quantity_kg, 2)); ?> kg</td>
              <td><?php echo e($order->formula->name); ?></td>
              <td class="d-flex gap-2">
                
                <a href="<?php echo e(route('production_orders.show', $order->id)); ?>" 
                   class="btn btn-sm btn-outline-info">
                  Görüntüle
                </a>

                
                <form action="<?php echo e(route('production_orders.addition', $order->id)); ?>" 
                      method="POST" 
                      onsubmit="return confirm('İlave olduğuna emin misin?');">
                  <?php echo csrf_field(); ?>
                  <button class="btn btn-sm btn-warning">İlave Ver</button>
                </form>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/production_orders/process.blade.php ENDPATH**/ ?>