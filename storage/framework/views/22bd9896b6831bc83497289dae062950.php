

<?php $__env->startSection('title','Denetim Takip Dokümanları'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mt-4">
    <h3>📑 Denetim Takip Dokümanları</h3>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="accordion" id="denetimAccordion">

        <?php
            $categories = [
                'yasal_statü' => '⚖️ Yasal Statü',
                'mesleki_sorumluluk_sigortası' => '🛡️ Mesleki Sorumluluk Sigortası',
                'imza_sirküleri' => '✍️ İmza Sirküleri',
                'hizmet_kapsamı' => '📌 Hizmet Kapsamı',
                'organizasyon_şeması' => '👥 Organizasyon Şeması ve Görev Tanımları',
                'laboratuvar_bilgi_beyan_formu' => '🧾 Laboratuvar Bilgi Beyan Formu',
                'ana_dokuman_listesi' => '📚 Ana Doküman Listesi',
                'kalite_el_kitabı' => '📖 Kalite El Kitabı',
                'prosedürler' => '📑 Prosedürler',
                'kalite_talimatları' => '📋 Kalite Talimatları',
                'cihaz_talimatları' => '🔧 Cihaz Talimatları',
                'kalibrasyon_planı' => '📐 Kalibrasyon Planı',
                'kalibrasyon_sertifikaları' => '📜 Kalibrasyon Sertifikaları',
                'referans_malzeme_listesi' => '🧪 Referans Malzeme Listesi ve Sertifikalar',
                'onaylı_tedarikçi_listesi' => '📦 Onaylı Tedarikçi Listesi ve Değerlendirmeler',
                'doğrulama_kayıtları' => '✅ Doğrulama ve Ölçüm Belirsizlikleri Kayıtları',
                'yeterlilik_deneyi_kayıtları' => '🧬 Yeterlilik Deneyi Kayıtları',
                'deney_raporu_örneği' => '📄 Deney Raporu Örneği',
                'risk_fırsat_değerlendirme' => '⚠️ Risk ve Fırsatları Değerlendirme',
                'iç_tetkik_kayıtları' => '🔍 İç Tetkik Kayıtları',
                'ygg' => '📊 YGG',
                'personel_listesi' => '👤 Personel Listesi',
                'deney_listesi' => '🧪 Deney Listesi',
                'iç_sözleşme' => '📑 İç Sözleşme',
                'uygunsuzluk_listesi' => '⚠️ Uygunsuzluk Listesi',
            ];
        ?>

        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="accordion-item mb-3"> <!-- boşluk için mb-3 -->
            <h2 class="accordion-header" id="heading<?php echo e($key); ?>">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo e($key); ?>">
                    <?php echo e($label); ?>

                </button>
            </h2>
            <div id="collapse<?php echo e($key); ?>" class="accordion-collapse collapse" data-bs-parent="#denetimAccordion">
                <div class="accordion-body">
                    <!-- Upload Form -->
                    <form action="<?php echo e(route('denetim.store')); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="category" value="<?php echo e($key); ?>">
                        <div class="mb-3">
                            <label>Doküman Adı</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>PDF Dosyası</label>
                            <input type="file" name="file" class="form-control" accept="application/pdf" required>
                        </div>
                        <div class="mb-3">
                            <label>Tanım</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Tarih</label>
                            <input type="date" name="date" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Yükle</button>
                    </form>

                    <!-- Listeleme -->
                    <table class="table mt-3">
                        <thead>
                            <tr>
                                <th>Adı</th>
                                <th>Tanım</th>
                                <th>Tarih</th>
                                <th>Ekleyen</th>
                                <th>Dosya</th>
                                <th>Sil</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $documents->where('category',$key); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($doc->title); ?></td>
                                    <td><?php echo e($doc->description); ?></td>
                                    <td><?php echo e($doc->date); ?></td>
                                    <td><?php echo e($doc->user->name); ?></td>
                                    <td><a href="<?php echo e(asset($doc->file_path)); ?>" target="_blank">PDF</a></td>
                                    <td>
                                        <form action="<?php echo e(route('denetim.destroy',$doc->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/denetim/index.blade.php ENDPATH**/ ?>