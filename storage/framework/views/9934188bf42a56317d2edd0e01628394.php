

<?php $__env->startSection('title', 'Ana Sayfa'); ?>

<?php $__env->startSection('content'); ?>


<div class="row">
  <div class="col-md-12">
    <h4>Planlanan İş Emirleri Takvimi</h4>
    <div id="calendar"></div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>

<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      locale: 'tr',
      height: 'auto',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,listWeek'
      },
      events: <?php echo json_encode($events, 15, 512) ?>,
      eventClick: function(info) {
        info.jsEvent.preventDefault();
        if (info.event.url) {
          window.location.href = info.event.url;
        }
      }
    });

    calendar.render();
  });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\manufacturer\resources\views/pages/home.blade.php ENDPATH**/ ?>