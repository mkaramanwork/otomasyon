@extends('layouts.admin')
@section('title','Doküman Ekle')
@section('content')
<div class="container">
    <h4>➕ Yeni Doküman Ekle</h4>

    <form action="{{ route('documents.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Doküman No</label>
            <input type="text" name="doc_no" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Doküman Adı</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Revizyon No</label>
            <input type="number" name="revision_no" class="form-control" value="0" required>
        </div>

        <div class="mb-3">
            <label>Yayın Tarihi</label>
            <input type="date" name="publish_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Son Revizyon Tarihi</label>
            <input type="date" name="last_revision_date" class="form-control">
        </div>

        <div class="mb-3">
            <label>Gözden Geçirme Tarihi</label>
            <input type="date" name="review_date" class="form-control">
        </div>

        <div class="mb-3">
            <label>Revizyon Nedeni</label>
            <input type="text" name="revision_reason" class="form-control">
        </div>

        <div class="mb-3">
            <label>Durum</label>
            <select name="status" class="form-control">
                <option value="guncel">Güncel</option>
                <option value="arsiv">Arşiv</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Kaydet</button>
    </form>
</div>
@endsection