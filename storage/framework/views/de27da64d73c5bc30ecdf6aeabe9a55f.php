

<?php $__env->startSection('title', 'Laboratuvar Takip Paneli'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
  <h2 class="text-center my-4">ALKİM Petrokimya Laboratuvar Numune Durumları</h2>

  
  <div class="text-center mb-4">
    <h4>Bugün: <?php echo e(now()->format('d.m.Y')); ?></h4>
  </div>

  
  <?php
    $days = [
      now()->subDays(2)->format('d.m.Y'),
      now()->subDays(1)->format('d.m.Y'),
      now()->format('d.m.Y'),
    ];
  ?>

  <div class="row mb-4 text-center">
    <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php
        $acceptedCount = $samples->whereBetween('taken_at', [\Carbon\Carbon::parse($day)->startOfDay(), \Carbon\Carbon::parse($day)->endOfDay()])
                                 ->count();

        $reportedCount = $samples->where('status','reported')
                                 ->whereBetween('taken_at', [\Carbon\Carbon::parse($day)->startOfDay(), \Carbon\Carbon::parse($day)->endOfDay()])
                                 ->count();
      ?>
      <div class="col-md-4">
        <div class="card">
          <div class="card-header bg-dark text-white">
            <?php echo e($day); ?>

          </div>
          <div class="card-body">
            <p class="text-success mb-1">Kabul Edilen: <strong><?php echo e($acceptedCount); ?></strong></p>
            <p class="text-primary mb-0">Raporlanan: <strong><?php echo e($reportedCount); ?></strong></p>
          </div>
        </div>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>

  
  <table class="table table-bordered table-striped table-hover text-center">
    <thead class="table-dark">
      <tr>
        <th>Batch No</th>
        <th>Ürün Adı</th>
        <th>Ülke</th>
        <th>Kabul Tarihi</th>
        <th>Durum</th>
        <th>Güncelleme</th>
      </tr>
    </thead>
    <tbody>
      <?php $__currentLoopData = $samples; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sample): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td><?php echo e($sample->batch_no); ?></td>
          <td><?php echo e($sample->labProduct->product_name ?? '-'); ?></td>
          <td><?php echo e($sample->country ?? '-'); ?></td>
          <td><?php echo e($sample->taken_at?->format('d.m.Y H:i') ?? '-'); ?></td>
          <td>
            <?php if($sample->status === 'accepted'): ?>
              <span class="badge bg-success">Kabul Edildi</span>
            <?php elseif($sample->status === 'reported'): ?>
              <span class="badge bg-primary">Raporlandı</span>
            <?php else: ?>
              <span class="badge bg-secondary">Beklemede</span>
            <?php endif; ?>
          </td>
          <td><?php echo e($sample->updated_at->diffForHumans()); ?></td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
  </table>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    setInterval(() => {
        window.location.reload();
    }, 15000); // 15 saniyede bir yenile
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.tv', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/labtv/show.blade.php ENDPATH**/ ?>