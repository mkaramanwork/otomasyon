<?php $__env->startSection('title','SDS Yönetimi'); ?>
<?php $__env->startSection('content'); ?>
<?php $editing = request('edit') ? $rows->firstWhere('id',(int)request('edit')) : null; ?>
<div class="container-fluid">
 <h3>SDS Yönetimi: <?php echo e(['companies'=>'Şirketler','families'=>'Ürün Aileleri','templates'=>'Şablonlar','preparers'=>'Hazırlayıcılar'][$type]); ?></h3>
 <div class="d-flex gap-2 flex-wrap my-3">
 <?php $__currentLoopData = ['companies'=>'Şirketler','families'=>'Ürün Aileleri','templates'=>'Şablonlar','preparers'=>'Hazırlayıcılar']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
 <a class="btn btn-<?php echo e($type===$code?'primary':'outline-secondary'); ?> btn-sm" href="<?php echo e(route('sds.management.index',$code)); ?>"><?php echo e($label); ?></a>
 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 </div>
 <?php if(session('success')): ?><div class="alert alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
 <?php if($errors->any()): ?><div class="alert alert-danger"><?php echo e(implode(' | ',$errors->all())); ?></div><?php endif; ?>
 <div class="card mb-4"><div class="card-header"><strong><?php echo e($editing?'Kaydı Düzenle':'Yeni Kayıt'); ?></strong></div><div class="card-body">
 <form method="POST" enctype="multipart/form-data" action="<?php echo e(route('sds.management.save',$type)); ?>"><?php echo csrf_field(); ?>
 <?php if($editing): ?><input type="hidden" name="id" value="<?php echo e($editing->id); ?>"><?php endif; ?>
 <div class="row g-3">
 <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
 <div class="col-md-<?php echo e($key==='address'?12:6); ?>"><label class="form-label"><?php echo e($label); ?></label>
 <?php if($key==='company_id'): ?>
 <select name="company_id" class="form-select" required><option value="">Firma seç</option><?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if((string)old($key,$editing->{$key}??'')===(string)$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
 <?php elseif($key==='logo_upload'): ?>
  <?php
    $currentLogo = $editing?->logo_path;
    $logoUrl = $currentLogo ? (preg_match('~^https?://~i', $currentLogo) ? $currentLogo : (str_starts_with($currentLogo, 'sds/logos/') ? asset($currentLogo) : asset('storage/' . ltrim($currentLogo, '/')))) : null;
  ?>
  <input type="file" accept="image/png,image/jpeg,image/webp" name="logo_upload" class="form-control" id="sds-logo-file">
  <small class="text-muted">PNG, JPG veya WebP · En fazla 2 MB. Yeni dosya seçmezsen mevcut logo kalır. Baskıda en-boy oranı korunarak sığdırılır.</small>
  <div class="mt-2 border rounded bg-light p-2" style="height:108px;display:flex;align-items:center;justify-content:center;">
    <img id="sds-logo-preview" src="<?php echo e($logoUrl ?: ''); ?>" alt="Logo önizleme" style="max-width:100%;max-height:88px;object-fit:contain;<?php echo e($logoUrl ? '' : 'display:none;'); ?>">
    <span id="sds-logo-placeholder" class="text-muted small" style="<?php echo e($logoUrl ? 'display:none;' : ''); ?>">Logo seçilmedi</span>
  </div>
  <?php if($currentLogo): ?>
    <label class="d-block mt-2"><input type="checkbox" name="remove_logo" value="1"> Mevcut logoyu kaldır</label>
  <?php endif; ?>
 <?php elseif(in_array($key,['issued_at','expires_at'],true)): ?>
  <?php $dateValue = old($key, $editing && $editing->{$key} ? $editing->{$key}->format('Y-m-d') : ''); ?>
  <input type="date" class="form-control" name="<?php echo e($key); ?>" value="<?php echo e($dateValue); ?>" lang="tr-TR">
  <small class="text-muted">Takvim simgesinden tarihi seçebilirsin.</small>
 <?php elseif($key==='address'): ?><textarea class="form-control" rows="3" name="<?php echo e($key); ?>"><?php echo e(old($key, $editing->{$key} ?? '')); ?></textarea>
 <?php elseif(in_array($key,['header_color','accent_color'],true)): ?>
  <?php $colorValue = old($key, $editing->{$key} ?? ($key==='header_color'?'#222222':'#e9ecef')); ?>
  <div class="d-flex align-items-center gap-2">
   <input type="color" class="form-control form-control-color" id="sds-color-<?php echo e($key); ?>" name="<?php echo e($key); ?>" value="<?php echo e($colorValue); ?>" title="Renk seç" style="width:72px;height:43px;cursor:pointer;">
   <span class="small text-muted" id="sds-color-text-<?php echo e($key); ?>"><?php echo e($colorValue); ?></span>
  </div>
 <?php else: ?> <input class="form-control" name="<?php echo e($key); ?>" value="<?php echo e(old($key,$editing->{$key}??'')); ?>" <?php echo e($key==='name'?'required':''); ?>><?php endif; ?>
 </div>
 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 </div><button class="btn btn-success mt-3">Kaydet</button> <?php if($editing): ?><a class="btn btn-outline-secondary mt-3" href="<?php echo e(route('sds.management.index',$type)); ?>">Vazgeç</a><?php endif; ?>
 </form></div></div>
 <div class="card"><div class="card-header">Kayıtlar</div><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>ID</th><th>Ad</th><th>Diğer</th><th>İşlem</th></tr></thead><tbody>
 <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><tr><td><?php echo e($row->id); ?></td><td><?php echo e($row->name); ?></td><td><?php echo e($row->code ?? $row->certificate_number ?? $row->email ?? ''); ?></td><td><a class="btn btn-outline-primary btn-sm" href="<?php echo e(route('sds.management.index',['type'=>$type,'edit'=>$row->id])); ?>">Düzenle</a></td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 </tbody></table></div></div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
 for (const field of ['header_color','accent_color']) {
   const picker = document.getElementById('sds-color-' + field);
   const label = document.getElementById('sds-color-text-' + field);
   if (picker && label) picker.addEventListener('input', function () { label.textContent = picker.value; });
 }
 const file = document.getElementById('sds-logo-file');
 const preview = document.getElementById('sds-logo-preview');
 const placeholder = document.getElementById('sds-logo-placeholder');
 if (!file || !preview) return;
 file.addEventListener('change', function () {
   const selected = file.files && file.files[0];
   if (!selected) return;
   if (!['image/png','image/jpeg','image/webp'].includes(selected.type) || selected.size > 2*1024*1024) {
     alert('PNG, JPG veya WebP formatında en fazla 2 MB logo seçiniz.');
     file.value=''; return;
   }
   const objectUrl = URL.createObjectURL(selected);
   preview.onload = function () { URL.revokeObjectURL(objectUrl); };
   preview.src = objectUrl; preview.style.display='block';
   if (placeholder) placeholder.style.display='none';
 });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/sds/management/index.blade.php ENDPATH**/ ?>