@extends('layouts.admin')
@section('title','Doküman Güncelle')
@section('content')
<div class="container">
    <h4>✏️ Doküman Güncelle</h4>

    <form action="{{ route('documents.update',$document->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Doküman No</label>
            <input type="text" class="form-control" value="{{ $document->doc_no }}" disabled>
        </div>

        <div class="mb-3">
            <label>Doküman Adı</label>
            <input type="text" name="title" class="form-control" value="{{ $document->title }}" required>
        </div>

        <div class="mb-3">
            <label>Revizyon No</label>
            <input type="number" name="revision_no" class="form-control" value="{{ $document->revision_no }}" required>
        </div>

        <div class="mb-3">
            <label>Yayın Tarihi</label>
            <input type="date" name="publish_date" class="form-control" value="{{ $document->publish_date }}" required>
        </div>

        <div class="mb-3">
            <label>Son Revizyon Tarihi</label>
            <input type="date" name="last_revision_date" class="form-control" value="{{ $document->last_revision_date }}">
        </div>

        <div class="mb-3">
            <label>Gözden Geçirme Tarihi</label>
            <input type="date" name="review_date" class="form-control" value="{{ $document->review_date }}">
        </div>

        <div class="mb-3">
            <label>Revizyon Nedeni</label>
            <input type="text" name="revision_reason" class="form-control" value="{{ $document->revision_reason }}">
        </div>

        <div class="mb-3">
            <label>Durum</label>
            <select name="status" class="form-control">
                <option value="guncel" {{ $document->status=='guncel'?'selected':'' }}>Güncel</option>
                <option value="arsiv" {{ $document->status=='arsiv'?'selected':'' }}>Arşiv</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Güncelle</button>
    </form>
</div>
@endsection