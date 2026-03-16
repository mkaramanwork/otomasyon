@extends('layouts.admin')
@section('title','Sınav Detayı')
@section('content')
<div class="container">

    <!-- ✅ Sınav Bilgileri -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">📘 {{ $exam->title }} Detayı</div>
        <div class="card-body">
            <p><strong>Kategori:</strong> {{ $exam->category->name }}</p>
            <p><strong>Soru Sayısı:</strong> {{ $exam->questions->count() }}</p>
        </div>
    </div>

    <!-- ✅ Kullanıcı Atama Formu -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">👥 Kullanıcıya Atama</div>
        <div class="card-body">
            <form action="{{ route('exams.assign',$exam->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label>Kullanıcı Seç</label>
                    <select name="user_ids[]" id="userSelect" class="form-select" multiple required>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Birden fazla kullanıcı seçebilirsin.</small>
                </div>
                <button type="submit" class="btn btn-success">Ata</button>
            </form>
        </div>
    </div>

    <!-- ✅ Mevcut Atamalar -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">📋 Mevcut Atamalar</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kullanıcı</th>
                        <th>Durum</th>
                        <th>Atama Tarihi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exam->assignments as $assign)
                    <tr>
                        <td>{{ $assign->user->name }}</td>
                        <td>
                            @if($assign->completed)
                                <span class="badge bg-success">Tamamlandı</span>
                            @else
                                <span class="badge bg-warning">Beklemede</span>
                            @endif
                        </td>
                        <td>{{ $assign->assigned_at }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center">Henüz atama yok</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ✅ Seçilen Sorular -->
    <div class="card">
        <div class="card-header bg-primary text-white">📝 Seçilen Sorular</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Soru Metni</th>
                        <th>Kategori</th>
                        <th>Zorluk</th>
                        <th>Doğru Cevap</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exam->questions as $index => $q)
                    <tr>
                        <td>{{ $index+1 }}</td>
                        <td>{{ $q->question_text }}</td>
                        <td>{{ $q->category->name }}</td>
                        <td>{{ $q->difficulty }}</td>
                        <td>{{ $q->correct_option }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Bu sınava henüz soru eklenmemiş</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ✅ Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#userSelect').select2({
        placeholder: "Kullanıcı seçiniz",
        allowClear: true,
        width: '100%'
    });
});
</script>
@endsection