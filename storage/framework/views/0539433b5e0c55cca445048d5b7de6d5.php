

<?php $__env->startSection('title', 'Müşteri Listesi'); ?>

<?php $__env->startSection('content'); ?>
<?php if(session('success')): ?>
    <div id="success-alert" class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <?php echo e(session('success')); ?>

        <button type="button" class="close" data-dismiss="alert">×</button>
    </div>
<?php endif; ?>

<div class="row mb-3">
    <div class="col-md-6">
        <h4>Müşteri Listesi</h4>
    </div>
    <div class="col-md-6 text-right">
        <a href="<?php echo e(route('customers.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Ekle
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table id="datatable" class="table table-striped table-hover mb-0">
            <thead class="thead-dark">
                <tr>
                    <th>Firma Adı</th>
                    <th>Yetkili</th>
                    <th>E-posta</th>
                    <th>Telefon</th>
                    <th>Ekleyen</th>
                    <th class="text-right">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($customer->name); ?></td>
                        <td><?php echo e($customer->contact_person); ?></td>
                        <td><?php echo e($customer->email); ?></td>
                        <td><?php echo e($customer->phone); ?></td>
                        <td><?php echo e($customer->user->name ?? '-'); ?></td>
                        <td class="text-right">
                            <a href="<?php echo e(route('customers.edit', $customer->id)); ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo e(route('customers.destroy', $customer->id)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Silmek istediğine emin misin?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">Kayıtlı müşteri bulunamadı.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/customers/index.blade.php ENDPATH**/ ?>