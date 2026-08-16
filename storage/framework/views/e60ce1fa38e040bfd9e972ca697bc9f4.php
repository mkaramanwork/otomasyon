

<?php $__env->startSection('title','Numune Rapor Detayı'); ?>

<?php $__env->startSection('content'); ?>

<?php if(session('success')): ?>
    <div class="alert alert-success mt-3">
        <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>

<div class="container">

    <h4>📑 Numune Rapor Detayı</h4>

    <p><strong>Ürün:</strong> <?php echo e($sample->labProduct->product_name ?? '-'); ?></p>
    <p><strong>Firma:</strong> <?php echo e($sample->company_name); ?></p>
    <p><strong>Batch No:</strong> <?php echo e($sample->batch_no); ?></p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Parametre</th>
                <th>Metot</th>
                <th>Min</th>
                <th>Max</th>
                <th>Net</th>
                <th>Sonuç</th>
                <th>Durum</th>
            </tr>
        </thead>

        <tbody>
            <?php $__currentLoopData = $sample->parameters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $param): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                <?php
                    $limits = $sample->labProduct->parameters->firstWhere(
                        'parameter_id',
                        $param->id
                    );

                    $result = $param->pivot->result;

                    $min = $limits->min_value ?? '-';
                    $max = $limits->max_value ?? '-';
                    $net = $limits->net_value ?? '-';

                    $status = 'Bekliyor';
                    $class = '';

                    if (
                        ($result === '-' || is_null($result)) &&
                        $min === '-' &&
                        $max === '-' &&
                        $net === '-'
                    ) {
                        $status = 'Yorumsuz';
                        $class = 'table-secondary';

                    } elseif (!is_null($result) && $result !== '-') {

                        if ($min !== '-' && $max !== '-') {

                            if ($result < $min) {
                                $status = 'Altında';
                                $class = 'table-danger';

                            } elseif ($result > $max) {
                                $status = 'Üzerinde';
                                $class = 'table-danger';

                            } else {
                                $status = 'Uygun';
                                $class = 'table-success';
                            }

                        } elseif ($net !== '-') {

                            if ($result == $net) {
                                $status = 'Uygun';
                                $class = 'table-success';

                            } else {
                                $status = 'Uygun Değil';
                                $class = 'table-danger';
                            }

                        } else {
                            $status = 'Kontrol Ediniz';
                            $class = 'table-warning';
                        }
                    }
                ?>

                <tr class="<?php echo e($class); ?>">
                    <td><?php echo e($param->parameter); ?></td>
                    <td><?php echo e($param->method_tr ?? '-'); ?></td>
                    <td><?php echo e($min); ?></td>
                    <td><?php echo e($max); ?></td>
                    <td><?php echo e($net); ?></td>
                    <td><?php echo e($result ?? 'Bekliyor'); ?></td>
                    <td><?php echo e($status); ?></td>
                </tr>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <form
        method="POST"
        action="<?php echo e(route('samples.report.store')); ?>"
        enctype="multipart/form-data"
        class="d-flex gap-2 mt-3"
    >
        <?php echo csrf_field(); ?>

        <input
            type="hidden"
            name="sample_id"
            value="<?php echo e($sample->id); ?>"
        >

        <input
            type="date"
            name="report_date"
            value="<?php echo e($sample->report_date); ?>"
            class="form-control w-auto"
            required
        >

        <input
            type="text"
            name="description"
            value="<?php echo e($sample->report_description); ?>"
            class="form-control w-50"
            placeholder="Açıklama"
        >

        <input
            type="file"
            name="images[]"
            class="form-control w-auto"
            multiple
        >

        <button
            type="submit"
            class="btn btn-success"
        >
            Kaydet
        </button>
    </form>

    <?php if($sample->report_date): ?>

        <?php
            $qrFileName = "raporno-{$sample->report_no}.svg";
            $qrFilePath = public_path("reports/{$qrFileName}");
            $qrExists = file_exists($qrFilePath);
        ?>

        <div class="mt-3 d-flex align-items-start gap-4">

            <div>

                <a
                    href="<?php echo e(route('samples.report.generate',['sample'=>$sample->id,'lang'=>'TR'])); ?>"
                    class="btn btn-primary"
                >
                    Rapor Yaz (TR)
                </a>

                <a
                    href="<?php echo e(route('samples.report.generate',['sample'=>$sample->id,'lang'=>'EN'])); ?>"
                    class="btn btn-secondary"
                >
                    Rapor Yaz (EN)
                </a>

                <a
                    href="<?php echo e(route('samples.report.generate',['sample'=>$sample->id,'lang'=>'RU'])); ?>"
                    class="btn btn-dark"
                >
                    Rapor Yaz (RU)
                </a>

            </div>

            <?php if($qrExists): ?>

                <div class="text-center">

                    <img
                        src="<?php echo e(asset('reports/'.$qrFileName)); ?>"
                        alt="QR Kod"
                        style="width:80px; height:80px;"
                    >

                    <div class="mt-2">

                        <a
                            href="<?php echo e(asset('reports/'.$qrFileName)); ?>"
                            class="btn btn-sm btn-info"
                            download
                        >
                            📥 QR İndir
                        </a>

                    </div>

                    <small class="text-muted d-block mt-1">
                        Doğrulama için tarayın
                    </small>

                </div>

            <?php endif; ?>

        </div>

    <?php else: ?>

        <div class="alert alert-warning mt-3">
            ⚠ Önce rapor tarihi ve açıklama kaydedilmelidir.
        </div>

    <?php endif; ?>

    <div class="mt-4">

        <?php if($sample->report_date): ?>

            <p>
                <strong>📅 Rapor Tarihi:</strong>
                <?php echo e($sample->report_date->format('d.m.Y')); ?>

            </p>

        <?php endif; ?>

        <?php if(!empty($sample->report_images)): ?>

            <h5>📷 Rapor Resimleri</h5>

            <div class="d-flex flex-wrap gap-3">

                <?php $__currentLoopData = $sample->report_images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <div class="text-center">

                        <a
                            href="#"
                            data-bs-toggle="modal"
                            data-bs-target="#imageModal<?php echo e($index); ?>"
                        >

                            <img
                                src="<?php echo e(asset('reports/picture/thumbs/'.$img)); ?>"
                                alt="Rapor Resmi"
                                class="img-thumbnail"
                                style="width:150px; height:auto;"
                            >

                        </a>

                        <form
                            action="<?php echo e(route('samples.report.image.delete',$sample->id)); ?>"
                            method="POST"
                            class="mt-1"
                        >
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>

                            <input
                                type="hidden"
                                name="image"
                                value="<?php echo e($img); ?>"
                            >

                            <button
                                type="submit"
                                class="btn btn-sm btn-danger"
                            >
                                Sil
                            </button>

                        </form>

                    </div>

                    <div
                        class="modal fade"
                        id="imageModal<?php echo e($index); ?>"
                        tabindex="-1"
                        aria-hidden="true"
                    >

                        <div class="modal-dialog modal-dialog-centered modal-lg">

                            <div class="modal-content">

                                <div class="modal-body text-center">

                                    <img
                                        src="<?php echo e(asset('reports/picture/'.$img)); ?>"
                                        class="img-fluid"
                                        alt="Rapor Resmi"
                                    >

                                </div>

                            </div>

                        </div>

                    </div>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </div>

        <?php endif; ?>

    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/sample_acceptances/show.blade.php ENDPATH**/ ?>