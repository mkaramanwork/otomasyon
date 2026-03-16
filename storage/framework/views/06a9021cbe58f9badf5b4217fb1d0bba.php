
<?php $__env->startSection('title','Personel Detay'); ?>
<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>📄 Personel Detay</h4>

    <ul>
        <li><strong>Adı:</strong> <?php echo e($personnel->name); ?></li>
        <li><strong>Ünvanı:</strong> <?php echo e($personnel->title); ?></li>
        <li><strong>Görevi:</strong> <?php echo e($personnel->duty); ?></li>
        <li><strong>İşe Başlama:</strong> <?php echo e($personnel->start_date); ?></li>
        <li><strong>Vekili:</strong> <?php echo e($personnel->deputy); ?></li>
    </ul>

    <hr>
    <h5>Oryantasyon Planları</h5>
    <!-- Oryantasyon Planı Modal Butonu -->
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#orientationModal">
        ➕ Oryantasyon Planı
    </button>
    <ul>
        <?php $__currentLoopData = $personnel->orientationPlans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>
                <?php echo e($plan->department); ?> - <?php echo e($plan->purpose); ?>

                <?php if($plan->is_completed): ?> ✅ Tamamlandı <?php endif; ?>

                <!-- Faaliyet Modal Butonu -->
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#activityModal<?php echo e($plan->id); ?>">
                    ➕ Faaliyet Ekle
                </button>

                <!-- Tamamlama Modal Butonu -->
                <?php if(!$plan->is_completed): ?>
                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#completeModal<?php echo e($plan->id); ?>">
                    ✔️ Tamamla
                </button>
                <?php endif; ?>

                <!-- Faaliyetler Listesi -->
                <ul>
                    <?php $__currentLoopData = $plan->activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $act): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($act->activity); ?> (<?php echo e($act->trainer); ?>) <?php echo e($act->start_date); ?> - <?php echo e($act->end_date); ?> Sonuç: <?php echo e($act->result); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>

                <!-- Faaliyet Modal -->
                <div class="modal fade" id="activityModal<?php echo e($plan->id); ?>" tabindex="-1" aria-labelledby="activityModalLabel<?php echo e($plan->id); ?>" aria-hidden="true">
                  <div class="modal-dialog">
                    <form action="<?php echo e(route('activity.store',$plan->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="activityModalLabel<?php echo e($plan->id); ?>">Faaliyet Ekle</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                              <div class="mb-3">
                                  <label>Faaliyet</label>
                                  <input type="text" name="activity" class="form-control" required>
                              </div>
                              <div class="mb-3">
                                  <label>Eğitimi Veren</label>
                                  <input type="text" name="trainer" class="form-control" required>
                              </div>
                              <div class="mb-3">
                                  <label>Başlangıç Tarihi</label>
                                  <input type="date" name="start_date" class="form-control" required>
                              </div>
                              <div class="mb-3">
                                  <label>Bitiş Tarihi</label>
                                  <input type="date" name="end_date" class="form-control" required>
                              </div>
                              <div class="mb-3">
                                  <label>Sonuç</label>
                                  <input type="text" name="result" class="form-control">
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

                <!-- Tamamlama Modal -->
                <div class="modal fade" id="completeModal<?php echo e($plan->id); ?>" tabindex="-1" aria-labelledby="completeModalLabel<?php echo e($plan->id); ?>" aria-hidden="true">
                  <div class="modal-dialog">
                    <form action="<?php echo e(route('orientation.complete',$plan->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="completeModalLabel<?php echo e($plan->id); ?>">Oryantasyon Tamamlama</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                              <div class="mb-3">
                                  <label>Değerlendirme Tarihi</label>
                                  <input type="date" name="evaluation_date" class="form-control" required>
                              </div>
                              <div class="mb-3">
                                  <label>Onay Tarihi</label>
                                  <input type="date" name="approval_date" class="form-control" required>
                              </div>
                          </div>
                          <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Tamamla</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                          </div>
                        </div>
                    </form>
                  </div>
                </div>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>

    <!-- Oryantasyon Planı Modal -->
    <div class="modal fade" id="orientationModal" tabindex="-1" aria-labelledby="orientationModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form action="<?php echo e(route('orientation.store',$personnel->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="orientationModalLabel">Oryantasyon Planı Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                  <div class="mb-3">
                      <label>Bölüm</label>
                      <input type="text" name="department" class="form-control" required>
                  </div>
                  <div class="mb-3">
                      <label>Oryantasyon Amacı</label>
                      <textarea name="purpose" class="form-control" required></textarea>
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

    <hr>
    <h5>Yetkinlikler</h5>
    <!-- Yetkinlik Modal Butonu -->
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#competencyModal">
        ➕ Yetkinlik Ata
    </button>
    <ul>
        <?php $__currentLoopData = $personnel->competencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($c->method); ?> (Seviye: <?php echo e($c->pivot->level); ?>, Tarih: <?php echo e($c->pivot->granted_at); ?>)</li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>

    <!-- Yetkinlik Modal -->
    <div class="modal fade" id="competencyModal" tabindex="-1" aria-labelledby="competencyModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form action="<?php echo e(route('competency.assign',$personnel->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="competencyModalLabel">Yetkinlik Atama</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                  <div class="mb-3">
                      <label>Parametre/Metot</label>
                      <select name="competency_id" class="form-select" required>
                          <?php $__currentLoopData = \App\Models\Competency::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <option value="<?php echo e($c->id); ?>"><?php echo e($c->method); ?></option>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </select>
                  </div>
                  <div class="mb-3">
                      <label>Yetki Tarihi</label>
                      <input type="date" name="granted_at" class="form-control" required>
                                    <div class="mb-3">
                      <label>Seviye</label>
                      <input type="text" name="level" class="form-control">
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/personnel/show.blade.php ENDPATH**/ ?>