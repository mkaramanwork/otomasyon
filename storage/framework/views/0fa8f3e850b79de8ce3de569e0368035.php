<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
  <div class="sidebar-brand"><a href="<?php echo e(route('home')); ?>" class="brand-link"><img src="<?php echo e(asset('assets/admin/img/logo.png')); ?>" alt="MKWeP" class="brand-image opacity-75 shadow"><span class="brand-text fw-light">MKWeP</span></a></div>
  <div class="sidebar-wrapper"><nav class="mt-2"><ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation" aria-label="Main navigation" data-accordion="false" id="navigation">
<?php
 $menuItems=\Illuminate\Support\Facades\Schema::hasTable('mkwep_menu_items') ? \Illuminate\Support\Facades\DB::table('mkwep_menu_items')->where('is_active',1)->orderBy('sort_order')->orderBy('id')->get() : collect();
 $visible=$menuItems->filter(function($item) {
   if($item->item_type!=='link') return false;
   if(!$item->route_name || !\Illuminate\Support\Facades\Route::has($item->route_name)) return false;
   return \App\Support\MkwepAccess::allowed(auth()->user(),$item->module_key,'view');
 });
 // Eski V4 kayitlarindaki cift JSON kodlamayi da destekle.
 $routeParams = static function ($item): array {
   $raw = $item->route_params ?? null;
   for ($i = 0; $i < 2 && is_string($raw); $i++) {
     $decoded = json_decode($raw, true);
     if (json_last_error() !== JSON_ERROR_NONE) break;
     $raw = $decoded;
   }
   $params = is_array($raw) ? $raw : [];
   // SDS yonetimi route'u zorunlu {type} parametresi kullanir.
   if (($item->route_name ?? '') === 'sds.management.index' && empty($params['type'])) {
     $params['type'] = 'companies';
   }
   return $params;
 };
 $byParent=$visible->groupBy('parent_key');
 $activeParents=$visible->pluck('parent_key')->filter()->unique();
 // Basliklarin sadece altinda gorunebilir link/parent varsa gosterilmesi.
 // Baslik bolumu bir sonraki 'header' satirina kadar devam eder.
 $headersWithContent=[];
 $currentHeader=null;
 foreach($menuItems->whereNull('parent_key') as $rootItem) {
   if ($rootItem->item_type === 'header') {
     $currentHeader=$rootItem->id;
     continue;
   }
   $hasVisibleContent=($rootItem->item_type === 'link' && $visible->contains('id',$rootItem->id))
     || ($rootItem->item_type === 'parent' && $activeParents->contains($rootItem->item_key));
   if ($hasVisibleContent && $currentHeader !== null) {
     $headersWithContent[$currentHeader]=true;
   }
 }
?>
<?php $__currentLoopData = $menuItems->whereNull('parent_key'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
 <?php if($menu->item_type==='header'): ?>
   <?php if(!isset($headersWithContent[$menu->id])): ?> <?php continue; ?> <?php endif; ?>
   <?php if($menu->item_key==='system-header' && !(auth()->user()?->hasRole('admin'))): ?> <?php continue; ?> <?php endif; ?>
   <li class="nav-header"><?php echo e($menu->label); ?></li>
 <?php elseif($menu->item_type==='parent'): ?>
   <?php if(!$activeParents->contains($menu->item_key)): ?> <?php continue; ?> <?php endif; ?>
   <?php $childLinks=$byParent->get($menu->item_key,collect()); $isOpen=$childLinks->contains(fn($child)=>request()->routeIs($child->route_name)); ?>
   <li class="nav-item <?php echo e($isOpen ? 'menu-open' : ''); ?>">
    <a href="#" class="nav-link <?php echo e($isOpen ? 'active' : ''); ?>"><i class="nav-icon bi <?php echo e($menu->icon); ?>"></i><p><?php echo e($menu->label); ?><i class="nav-arrow bi bi-chevron-right"></i></p></a>
    <ul class="nav nav-treeview">
      <?php $__currentLoopData = $childLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
       <li class="nav-item"><a href="<?php echo e(route($child->route_name, $routeParams($child))); ?>" class="nav-link <?php echo e(request()->routeIs($child->route_name) ? 'active' : ''); ?>"><i class="nav-icon bi <?php echo e($child->icon); ?>"></i><p><?php echo e($child->label); ?></p></a></li>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
   </li>
 <?php elseif($menu->item_type==='link' && $visible->contains('id',$menu->id)): ?>
   <li class="nav-item"><a href="<?php echo e(route($menu->route_name,$routeParams($menu))); ?>" class="nav-link <?php echo e(request()->routeIs($menu->route_name) ? 'active' : ''); ?>"><i class="nav-icon bi <?php echo e($menu->icon); ?>"></i><p><?php echo e($menu->label); ?></p></a></li>
 <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </ul></nav></div>
</aside>
<?php /**PATH C:\laragon\www\manufacturer\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>