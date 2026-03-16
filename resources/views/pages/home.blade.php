@extends('layouts.admin')

@section('title', 'Ana Sayfa')

@section('content')


<div class="row">
  <div class="col-md-12">
    <h4>Planlanan İş Emirleri Takvimi</h4>
    <div id="calendar"></div>
  </div>
</div>
@endsection

@section('js')

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
      events: @json($events),
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
@endsection