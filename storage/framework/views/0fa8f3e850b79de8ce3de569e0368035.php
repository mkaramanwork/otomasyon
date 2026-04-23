<!--begin::Sidebar-->
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
  <!--begin::Sidebar Brand-->
  <div class="sidebar-brand">
    <a href="<?php echo e(route('home')); ?>" class="brand-link">
      <img src="<?php echo e(asset('assets/admin/img/logo.png')); ?>" alt="AdminLTE Logo"
           class="brand-image opacity-75 shadow"/>
      <span class="brand-text fw-light">KaramaN</span>
    </a>
  </div>
  <!--end::Sidebar Brand-->

  <!--begin::Sidebar Wrapper-->
  <div class="sidebar-wrapper">
    <nav class="mt-2">
      <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview"
          role="navigation" aria-label="Main navigation" data-accordion="false" id="navigation">

        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'admin')): ?>

        <!-- Ürün-Sipariş Alanı -->
        <li class="nav-item"><a href="<?php echo e(route('sales_products.index')); ?>" class="nav-link"><i class="nav-icon bi bi-pass-fill"></i><p>Ürün Listesi</p></a></li>
        <li class="nav-item"><a href="<?php echo e(route('orders.index')); ?>" class="nav-link"><i class="nav-icon bi bi-pen-fill"></i><p>Teklif-Sipariş Listesi</p></a></li>

        <!-- Stok Giriş Alanı -->
        <li class="nav-item"><a href="<?php echo e(route('quality-checks.index')); ?>" class="nav-link"><i class="nav-icon bi bi-clipboard-fill"></i><p>Kal. Kontrol-Stok Girişi</p></a></li>

        <!-- Üretim Alanı -->
        <li class="nav-item has-treeview">
          <a href="#" class="nav-link"><i class="nav-icon bi bi-browser-edge"></i><p>Üretim Modülü<i class="nav-arrow bi bi-chevron-right"></i></p></a>
          <ul class="nav nav-treeview">
            <li class="nav-item"><a href="<?php echo e(route('production_orders.calendar')); ?>" class="nav-link"><i class="nav-icon bi bi-calendar"></i><p>Plan Listesi</p></a></li>
            <li class="nav-item"><a href="<?php echo e(route('production_orders.index')); ?>" class="nav-link"><i class="nav-icon bi bi-list-check"></i><p>İş Emri Listesi</p></a></li>
            <li class="nav-item"><a href="<?php echo e(route('production_orders.process')); ?>" class="nav-link"><i class="nav-icon bi bi-hand-thumbs-up-fill"></i><p>Harmanlama</p></a></li>
            <li class="nav-item"><a href="<?php echo e(route('production_orders.filling')); ?>" class="nav-link"><i class="nav-icon bi bi-patch-check-fill"></i><p>Dolum Hattı</p></a></li>
                    <!-- Raporlama -->
            <li class="nav-item"><a href="<?php echo e(route('reports.fillings')); ?>" class="nav-link"><i class="nav-icon bi bi-palette"></i><p>Dolum Raporlama</p></a></li>

          </ul>
        </li>
        <?php endif; ?>
        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'admin|laboratuvarmüdürü')): ?>
          <!-- Laboratuvar -->
       
            <li class="nav-header">Laboratuvar Modülü</li>
            <li class="nav-item"><a href="<?php echo e(route('parameters.index')); ?>" class="nav-link"><i class="nav-icon bi bi-bookmark-check-fill"></i><p>Parametre Listesi</p></a></li>
            <li class="nav-item"><a href="<?php echo e(route('samples.index')); ?>" class="nav-link"><i class="nav-icon bi bi-beaker-fill"></i><p>Numune Kabul</p></a></li>
            <li class="nav-item"><a href="<?php echo e(route('samples.analysis-pending')); ?>" class="nav-link"><i class="nav-icon bi bi-flask-florence-fill"></i><p>Analizler</p></a></li>
            <li class="nav-item"><a href="<?php echo e(route('samples.report.page')); ?>" class="nav-link"><i class="nav-icon bi bi-file-earmark-text-fill"></i><p>Raporlar</p></a></li>
            <li class="nav-item"><a href="<?php echo e(route('labtv.show')); ?>" class="nav-link"><i class="nav-icon bi bi-aspect-ratio"></i><p>Lab TV</p></a></li>
            <li class="nav-item"><a href="<?php echo e(route('additions.index')); ?>" class="nav-link"><i class="nav-icon bi bi-arrow-down-square"></i><p>İlaveler</p></a></li>
            <li class="nav-item"><a href="<?php echo e(route('lab_products.index')); ?>" class="nav-link"><i class="nav-icon bi bi-database"></i><p>Ürün Listesi DB</p></a></li>

          <?php endif; ?>
          <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'admin')): ?>
                <!-- Satın Alma -->
        <li class="nav-item has-treeview">
          <a href="#" class="nav-link"><i class="nav-icon bi bi-credit-card"></i><p>Satın Alma Modülü<i class="nav-arrow bi bi-chevron-right"></i></p></a>
          <ul class="nav nav-treeview">
            <li class="nav-item"><a href="<?php echo e(route('suppliers.index')); ?>" class="nav-link"><i class="nav-icon bi bi-buildings-fill"></i><p>Onaylı Tedarikçiler</p></a></li>
            <li class="nav-item"><a href="<?php echo e(route('inventories.index')); ?>" class="nav-link"><i class="nav-icon bi bi-clipboard-check-fill"></i><p>Envanter</p></a></li>
            <li class="nav-item"><a href="<?php echo e(route('purchase-requests.index')); ?>" class="nav-link"><i class="nav-icon bi bi-emoji-smile-fill"></i><p>Satın Alma Talepleri</p></a></li>
          </ul>
        </li>
                <!-- KYS -->
        <li class="nav-item has-treeview">
          <a href="#" class="nav-link"><i class="nav-icon bi bi-folder-check"></i><p>KYS Modülü<i class="nav-arrow bi bi-chevron-right"></i></p></a>
          <ul class="nav nav-treeview">
            <li class="nav-item"><a href="<?php echo e(route('equipment.index')); ?>" class="nav-link"><i class="nav-icon bi bi-buildings-fill"></i><p>Cihazlarım</p></a></li>
            <li class="nav-item"><a href="<?php echo e(route('documents.index')); ?>" class="nav-link"><i class="nav-icon bi bi-clipboard-check-fill"></i><p>Dokümanlarım</p></a></li>
            <li class="nav-item"><a href="<?php echo e(route('personnel.index')); ?>" class="nav-link"><i class="nav-icon bi bi-emoji-smile-fill"></i><p>Personel</p></a></li>
            <li class="nav-item"><a href="<?php echo e(route('uygunsuzluk.index')); ?>" class="nav-link"><i class="nav-icon bi bi-folder-x"></i><p>Uygunsuzluklarım</p></a></li>
            <li class="nav-item"><a href="<?php echo e(route('tetkik.index')); ?>" class="nav-link"><i class="nav-icon bi bi-pencil"></i><p>İç Tetkik</p></a></li>
            <li class="nav-item"><a href="<?php echo e(route('external-documents.index')); ?>" class="nav-link"><i class="nav-icon bi bi-collection"></i><p>Dış Kaynaklı Doküman</p></a></li>
            <li class="nav-item"><a href="<?php echo e(route('exams.index')); ?>" class="nav-link"><i class="nav-icon bi bi-check2-circle"></i><p>Sınav Soruları</p></a></li>
            <li class="nav-item"><a href="<?php echo e(route('urunAgaci.index')); ?>" class="nav-link"><i class="nav-icon bi bi-bezier2"></i><p>Ürün Sicil Kartları</p></a></li>
            <li class="nav-item"><a href="<?php echo e(route('complaints.index')); ?>" class="nav-link"><i class="nav-icon bi bi-award"></i><p>Müşteri Öneri/Şikayet</p></a></li>

          </ul>

                  <!-- Denetim -->
        <li class="nav-header">Denetim Hazırlık Modülü</li>
        <li class="nav-item"><a href="<?php echo e(route('denetim.index')); ?>" class="nav-link"><i class="nav-icon bi bi-book-fill"></i><p>Denetim Listesi</p></a></li>
        </li>
                <!-- Stok Takip -->
        <li class="nav-header">TDS Hazırlık Modülü</li>
        <li class="nav-item"><a href="<?php echo e(route('tds.index')); ?>" class="nav-link"><i class="nav-icon bi bi-star-half"></i><p>TDS</p></a></li>


        <!-- Stok Takip -->
        <li class="nav-header">Stok Takip Alanı</li>
        <li class="nav-item"><a href="<?php echo e(route('reports.stoch')); ?>" class="nav-link"><i class="nav-icon bi bi-star-half"></i><p>Stok Listesi</p></a></li>

        <!-- MWEP TV -->
        <li class="nav-item"><a href="<?php echo e(route('tv.show')); ?>" class="nav-link"><i class="nav-icon bi bi-play-circle-fill"></i><p>MWEP TV</p></a></li>

        <!-- Veri Tabanı -->
        <li class="nav-header">Veri Tabanı</li>
        <li class="nav-item has-treeview">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-database-up"></i>
            <p>
              Veri Tabanı
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item"><a href="<?php echo e(route('products.index')); ?>" class="nav-link"><i class="nav-icon bi bi-circle"></i><p>Ürün Listesi</p></a></li>
            <li class="nav-item"><a href="<?php echo e(route('raw-materials.index')); ?>" class="nav-link"><i class="nav-icon bi bi-circle"></i><p>Ham Madde</p></a></li>
            <li class="nav-item"><a href="<?php echo e(route('physical_parameter.index')); ?>" class="nav-link"><i class="nav-icon bi bi-circle"></i><p>Fiziksel Ham Madde</p></a></li>
            <li class="nav-item"><a href="<?php echo e(route('customers.index')); ?>" class="nav-link"><i class="nav-icon bi bi-circle"></i><p>Müşteriler</p></a></li>
            <li class="nav-item"><a href="<?php echo e(route('production-lines.index')); ?>" class="nav-link"><i class="nav-icon bi bi-circle"></i><p>Üretim Hatları</p></a></li>
            <li class="nav-item"><a href="<?php echo e(route('formulas.index')); ?>" class="nav-link"><i class="nav-icon bi bi-circle"></i><p>Yarı Mamül Reçeteleri</p></a></li>
            <li class="nav-item"><a href="<?php echo e(route('physcial_formula.index')); ?>" class="nav-link"><i class="nav-icon bi bi-circle"></i><p>Fiziksel Reçeteler</p></a></li>
          </ul>
        </li>
        <?php endif; ?>
        
      </ul>
      <!--end::Sidebar Menu-->
    </nav>
  </div>
  <!--end::Sidebar Wrapper-->
</aside>
<!--end::Sidebar--><?php /**PATH C:\laragon\www\manufacturer\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>