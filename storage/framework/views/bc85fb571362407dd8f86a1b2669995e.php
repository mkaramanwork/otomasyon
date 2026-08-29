
<?php $__env->startSection('title','Kullanıcı Yönetimi'); ?>
<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>👥 Kullanıcı Listesi</h4>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Ad</th>
                <th>Email</th>
                <th>Roller</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($user->name); ?></td>
                <td><?php echo e($user->email); ?></td>
                <td>
                    <?php if($user->getRoleNames()->isNotEmpty()): ?>
                        <?php echo e($user->getRoleNames()->implode(', ')); ?>

                    <?php else: ?>
                        <span class="text-muted">Rol yok</span>
                    <?php endif; ?>
                </td>
                <td>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#roleModal<?php echo e($user->id); ?>">
                        Rol Güncelle
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="roleModal<?php echo e($user->id); ?>" tabindex="-1">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <form action="<?php echo e(route('users.updateRoles',$user->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                            <div class="modal-header">
                              <h5 class="modal-title"><?php echo e($user->name); ?> için Rol Güncelle</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                              <select name="roles[]" class="form-select" multiple>
                                  <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                      <option value="<?php echo e($role->name); ?>" <?php echo e($user->hasRole($role->name) ? 'selected' : ''); ?>>
                                          <?php echo e($role->name); ?>

                                      </option>
                                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                              </select>
                              <small class="text-muted">CTRL veya CMD ile birden fazla rol seçebilirsin.</small>
                            </div>
                            <div class="modal-footer">
                              <button type="submit" class="btn btn-success">Kaydet</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="4" class="text-center">Henüz kullanıcı yok</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/users/index.blade.php ENDPATH**/ ?>