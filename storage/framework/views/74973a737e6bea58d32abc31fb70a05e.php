
<?php $__env->startSection('title', 'İş Emirleri'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">İş Emirleri Listesi</h4>
        <a href="<?php echo e(route('production_orders.create')); ?>" class="btn btn-primary">
            + Yeni İş Emri Oluştur
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Batch No</th>
                    <th>Ürün</th>
                    <th>Hat</th>
                    <th>Planlanan Tarih</th>
                    <th>Tonaj</th>
                    <th>Durum</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td><?php echo e($order->batch_number); ?></td>
                        <td><?php echo e($order->product->name ?? '-'); ?></td>
                        <td><?php echo e($order->line->name ?? '-'); ?></td>
                        <td><?php echo e(\Carbon\Carbon::parse($order->planned_at)->format('d.m.Y H:i')); ?></td>
                        <td><?php echo e($order->quantity_kg); ?> <?php echo e($order->unit); ?></td>
                        <td>
                            <?php
                                $statusColors = [
                                    'planned' => 'secondary',
                                    'in_progress' => 'primary',
                                    'completed' => 'success',
                                    'cancelled' => 'danger',
                                ];
                            ?>
                            <span class="badge bg-<?php echo e($statusColors[$order->status] ?? 'dark'); ?>">
                                <?php echo e(strtoupper($order->status)); ?>

                            </span>
                        </td>
                        <td class="text-nowrap">
                            <a href="<?php echo e(route('production_orders.show', $order->id)); ?>" class="btn btn-sm btn-outline-info">
                                Detay
                            </a>
                            <a href="<?php echo e(route('production_orders.edit', $order->id)); ?>" class="btn btn-sm btn-outline-primary">
                                Düzenle
                            </a>
                            <form action="<?php echo e(route('production_orders.destroy', $order->id)); ?>" method="POST" class="d-inline-block" onsubmit="return confirm('İş emrini silmek istediğine emin misin?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">Sil</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">Henüz iş emri oluşturulmamış.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/production_orders/index.blade.php ENDPATH**/ ?>