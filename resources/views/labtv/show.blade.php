@extends('layouts.tv')

@section('title', 'Laboratuvar Takip Paneli')

@section('content')
<div class="container-fluid">
  <h2 class="text-center my-4">ALKİM Petrokimya Laboratuvar Numune Durumları</h2>

  {{-- ✅ Günün tarihi --}}
  <div class="text-center mb-4">
    <h4>Bugün: {{ now()->format('d.m.Y') }}</h4>
  </div>

  {{-- ✅ Son 3 gün özet bilgileri --}}
  @php
    $days = [
      now()->subDays(2)->format('d.m.Y'),
      now()->subDays(1)->format('d.m.Y'),
      now()->format('d.m.Y'),
    ];
  @endphp

  <div class="row mb-4 text-center">
    @foreach($days as $day)
      @php
        $acceptedCount = $samples->whereBetween('taken_at', [\Carbon\Carbon::parse($day)->startOfDay(), \Carbon\Carbon::parse($day)->endOfDay()])
                                 ->count(); // ✅ Kabul edilenler her zaman sayılıyor

        $reportedCount = $samples->where('status','reported')
                                 ->whereBetween('taken_at', [\Carbon\Carbon::parse($day)->startOfDay(), \Carbon\Carbon::parse($day)->endOfDay()])
                                 ->count();
      @endphp
      <div class="col-md-4"> {{-- ✅ 3 sütun yan yana --}}
        <div class="card">
          <div class="card-header bg-dark text-white">
            {{ $day }}
          </div>
          <div class="card-body">
            <p class="text-success mb-1">Kabul Edilen: <strong>{{ $acceptedCount }}</strong></p>
            <p class="text-primary mb-0">Raporlanan: <strong>{{ $reportedCount }}</strong></p>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- ✅ Ana tablo --}}
  <table class="table table-bordered table-striped table-hover text-center">
    <thead class="table-dark">
      <tr>
        <th>Batch No</th>
        <th>Numune Adı</th>
        <th>Ülke</th>
        <th>Kabul Tarihi</th>
        <th>Durum</th>
        <th>Güncelleme</th>
      </tr>
    </thead>
    <tbody>
      @foreach($samples as $sample)
        <tr>
          <td>{{ $sample->batch_no }}</td>
          <td>{{ $sample->product->name ?? '-' }}</td>
          <td>{{ $sample->country ?? '-' }}</td>
          <td>{{ $sample->taken_at?->format('d.m.Y H:i') ?? '-' }}</td>
          <td>
            @if($sample->status === 'accepted')
              <span class="badge bg-success">Kabul Edildi</span>
            @elseif($sample->status === 'reported')
              <span class="badge bg-primary">Raporlandı</span>
            @else
              <span class="badge bg-secondary">Beklemede</span>
            @endif
          </td>
          <td>{{ $sample->updated_at->diffForHumans() }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection

@section('scripts')
<script>
    setInterval(() => {
        window.location.reload();
    }, 15000); // 15 saniyede bir yenile
</script>
@endsection