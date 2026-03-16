@extends('layouts.admin')

@section('content')
<div class="container">
    <h4>Doküman Güncelle</h4>

    <!-- Uyarı kutusu -->
    <div class="alert alert-warning">
        <strong>Dikkat!</strong> Revizyon numarası değişirse eski veriler arşive alınacaktır.
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                   <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('external-documents.update',$doc->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Sıra No</label>
            <input type="number" name="sequence_no" class="form-control" value="{{ $doc->sequence_no }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Doküman Kodu</label>
            <input type="text" name="document_code" class="form-control" value="{{ $doc->document_code }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Doküman Adı</label>
            <input type="text" name="title" class="form-control" value="{{ $doc->title }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Kurum</label>
            <input type="text" name="institution" class="form-control" value="{{ $doc->institution }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Revizyon No</label>
            <input type="text" name="revision_no" class="form-control" value="{{ $doc->revision_no }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Yayın Tarihi</label>
            <input type="date" name="publish_date" class="form-control" value="{{ $doc->publish_date }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Gözden Geçirilme Tarihi</label>
            <input type="date" name="review_date" class="form-control" value="{{ $doc->review_date }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Kaynak URL</label>
            <input type="text" name="url" class="form-control" value="{{ $doc->url }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Yeni Dosya (PDF, Word, PowerPoint)</label>
            <input type="file" name="file" class="form-control">
            <small class="text-muted">Mevcut dosya: <a href="{{ Storage::url($doc->file_path) }}" target="_blank">İndir</a></small>
        </div>

        <button type="submit" class="btn btn-success">Güncelle</button>
        <a href="{{ route('external-documents.index') }}" class="btn btn-secondary">İptal</a>
    </form>
</div>
@endsection