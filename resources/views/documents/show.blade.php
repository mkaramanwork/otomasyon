@extends('layouts.admin')
@section('title','Doküman Detay')
@section('content')
<div class="container">
    <h4>{{ $document->title }} ({{ $document->doc_no }})</h4>

    <ul class="list-group mb-3">
        <li class="list-group-item"><strong>Revizyon No:</strong> R{{ $document->revision_no }}</li>
        <li class="list-group-item"><strong>Yayın Tarihi:</strong> {{ $document->publish_date }}</li>
        <li class="list-group-item"><strong>Son Revizyon Tarihi:</strong> {{ $document->last_revision_date }}</li>
        <li class="list-group-item"><strong>Gözden Geçirme Tarihi:</strong> {{ $document->review_date }}</li>
        <li class="list-group-item"><strong>Revizyon Nedeni:</strong> {{ $document->revision_reason }}</li>
        <li class="list-group-item"><strong>Kayıt Eden:</strong> {{ $document->created_by }}</li>
        <li class="list-group-item"><strong>Durum:</strong>
            <span class="badge bg-{{ $document->status == 'guncel' ? 'success' : 'secondary' }}">
                {{ ucfirst($document->status) }}
            </span>
        </li>
    </ul>

    <!-- Revizyon Geçmişi -->
    <div class="card mb-4">
        <div class="card-header">📂 Revizyon Geçmişi</div>
        <div class="card-body">
            <table class="table table-sm table-bordered">
                <thead><tr><th>Revizyon</th><th>Tarih</th><th>Nedeni</th><th>Dosya</th></tr></thead>
                <tbody>
                    @forelse($document->revisions as $rev)
                        <tr>
                            <td>R{{ $rev->revision_no }}</td>
                            <td>{{ $rev->revision_date }}</td>
                            <td>{{ $rev->change_note }}</td>
                            <td><a href="{{ asset('storage/'.$rev->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">Görüntüle</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">Henüz revizyon yok</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Yeni Revizyon Ekle -->
    <div class="card mb-4">
        <div class="card-header">➕ Yeni Revizyon Ekle</div>
        <div class="card-body">
            <form action="{{ route('documents.revision.store',$document->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-2">
                    <label>Revizyon Nedeni</label>
                    <input type="text" name="change_note" class="form-control">
                </div>
                <div class="mb-2">
                    <label>Dosya (PDF/DOCX)</label>
                    <input type="file" name="file" class="form-control" accept=".pdf,.docx" required>
                </div>
                <div class="mb-2">
                    <label>Revizyon Tarihi</label>
                    <input type="date" name="revision_date" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label>Gözden Geçirme Tarihi</label>
                    <input type="date" name="review_date" class="form-control">
                </div>
                <button type="submit" class="btn btn-success">Kaydet</button>
            </form>
        </div>
    </div>
</div>
@endsection