@extends('layouts.admin')
@section('title','Sınav Yönetimi')
@section('content')
<div class="container">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>📘 Sınav Listesi</h4>
    <div class="ms-auto">
        <a href="{{ route('questions.index') }}" class="btn btn-warning me-2">
            <i class="fas fa-list"></i> Soru Listesi
        </a>
        <a href="{{ route('questions.create') }}" class="btn btn-danger me-2">
            <i class="fas fa-question"></i> Soru Ekle
        </a>
        <a href="{{ route('exams.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Yeni Sınav Oluştur
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<!-- ✅ Kritik Sınavlar -->
@if($criticalExams->count())
<div class="card mb-4">
    <div class="card-header bg-danger text-white">⚠️ Çözülmesi Gereken Kritik Sınavlar</div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Başlık</th>
                    <th>Kategori</th>
                    <th>Soru Sayısı</th>
                    <th>Durum</th>
                    <th>Çöz</th>
                    <th>Detay</th>
                    <th>İşlemler</th>
                    <th>QR Kod</th>
                </tr>
            </thead>
            <tbody>
                @foreach($criticalExams as $exam)
                <tr>
                    <td>{{ $exam->title }}</td>
                    <td>{{ $exam->category->name }}</td>
                    <td>{{ $exam->question_count }}</td>
                    <td><span class="badge bg-danger">Çözülmedi</span></td>
                    <td><a href="{{ route('exams.solve',$exam->id) }}" class="btn btn-info btn-sm">Çöz</a></td>
                    <td><a href="{{ route('exams.show',$exam->id) }}" class="btn btn-primary btn-sm">Göster</a></td> <!-- ✅ -->
                    <td>
                        <a href="{{ route('exams.edit',$exam->id) }}" class="btn btn-warning btn-sm">Güncelle</a>
                        <form action="{{ route('exams.destroy',$exam->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Silmek istediğine emin misin?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                        </form>
                    </td>
                    <td>{!! QrCode::size(80)->generate(route('exams.solve',$exam->id)) !!}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- ✅ Genel Sınav Listesi -->
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Başlık</th>
            <th>Kategori</th>
            <th>Soru Sayısı</th>
            <th>Durum</th>
            <th>Sonuç</th>
            <th>Detay</th>
            <th>İşlemler</th>
            <th>QR Kod</th>
        </tr>
    </thead>
    <tbody>
        @forelse($exams as $exam)
        <tr>
            <td>{{ $exam->title }}</td>
            <td>{{ $exam->category->name }}</td>
            <td>{{ $exam->question_count }}</td>
            <td>
                @if($exam->assignments->where('completed',0)->count())
                    <span class="badge bg-warning">Beklemede</span>
                @else
                    <span class="badge bg-success">Tamamlandı</span>
                @endif
            </td>
            <td><a href="{{ route('exams.results',$exam->id) }}" class="btn btn-info btn-sm">Sonuç</a></td>
            <td><a href="{{ route('exams.show',$exam->id) }}" class="btn btn-primary btn-sm">Göster</a></td> <!-- ✅ -->
            <td>
                <a href="{{ route('exams.edit',$exam->id) }}" class="btn btn-warning btn-sm">Güncelle</a>
                <form action="{{ route('exams.destroy',$exam->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Silmek istediğine emin misin?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                </form>
            </td>
            <td>{!! QrCode::size(80)->generate(route('exams.solve',$exam->id)) !!}</td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center">Henüz sınav tanımlı değil</td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>
@endsection