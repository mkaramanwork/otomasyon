<p>Merhaba <?php echo e($sample->customer->contact_person ?? 'Yetkili'); ?>,</p>

<p>Numune raporunuz hazırlanmıştır. Özet bilgileri aşağıda bulabilirsiniz:</p>

<ul>
    <li><strong>Firma:</strong> <?php echo e($sample->company_name); ?></li>
    <li><strong>Ürün:</strong> <?php echo e($sample->product->name); ?></li>
    <li><strong>Batch No:</strong> <?php echo e($sample->batch_no); ?></li>
    <li><strong>Rapor No:</strong> <?php echo e($sample->report_no); ?></li>
    <li><strong>Rapor Tarihi:</strong> <?php echo e($sample->report_date?->format('d.m.Y')); ?></li>
    <li><strong>Açıklama:</strong> <?php echo e($sample->report_description); ?></li>
</ul>

<h4>Parametreler ve Sonuçlar (Özet)</h4>
<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse;">
    <thead>
        <tr>
            <th>Parametre</th>
            <th>Sonuç</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $sample->parameters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $param): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($param->parameter); ?></td>
                <td><?php echo e($param->pivot->result ?? 'Bekliyor'); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>

<p>Saygılar,<br>Murat KARAMAN ERP v0.1</p><?php /**PATH C:\laragon\www\manufacturer\resources\views/sample_acceptances/mail.blade.php ENDPATH**/ ?>