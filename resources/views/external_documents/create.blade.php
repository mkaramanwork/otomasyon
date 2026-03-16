@extends('layouts.admin')

@section('content')
<div class="container">
    <h4>Yeni Dış Kaynaklı Doküman</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                   <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('external-documents.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Sıra No</label>
            <input type="number" name="sequence_no" class="form-control" value="{{ $nextSeq }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Doküman Kodu</label>
            <input type="text" name="document_code" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Doküman Adı</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Kurum</label>
            <input type="text" name="institution" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Revizyon No</label>
            <input type="text" name="revision_no" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Yayın Tarihi</label>
            <input type="date" name="publish_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Gözden Geçirilme Tarihi</label>
            <input type="date" name="review_date" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Kaynak URL</label>
            <input type="text" name="url" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Dosya (PDF, Word, PowerPoint)</label>
            <input type="file" name="file" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Kaydet</button>
        <a href="{{ route('external-documents.index') }}" class="btn btn-secondary">İptal</a>
    </form>
</div>
@endsection