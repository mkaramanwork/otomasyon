@extends('layouts.admin')

@section('content')
<div class="container">
    <h4>Doküman Detayı</h4>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Sıra No:</strong> {{ $doc->sequence_no }}</p>
            <p><strong>Kod:</strong> {{ $doc->document_code }}</p>
            <p><strong>Ad:</strong> {{ $doc->title }}</p>
            <p><strong>Kurum:</strong> {{ $doc->institution }}</p>
            <p><strong>Revizyon:</strong> {{ $doc->revision_no }}</p>
            <p><strong>Yayın Tarihi:</strong> {{ $doc->publish_date }}</p>
            <p><strong>Gözden Geçirilme Tarihi:</strong> {{ $doc->review_date }}</p>
            <p><strong>Kaynak URL:</strong> 
                @if($doc->url)
                    <a href="{{ $doc->url }}" target="_blank">{{ $doc->url }}</a>
                @else
                    -
                @endif
            </p>
            <p><strong>Dosya:</strong> 
                <a href="{{ Storage::url($doc->file_path) }}" target="_blank">İndir</a>
            </p>
            <p><strong>Kayıt Eden:</strong> {{ $doc->user->name ?? '-' }}</p>
        </div>
    </div>

    <h5>Eski Revizyonlar</h5>
    @if($doc->revisions->count() > 0)
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Revizyon No</th>
                    <th>Yayın Tarihi</th>
                    <th>Dosya</th>
                    <th>Eklenme Tarihi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($doc->revisions as $rev)
                <tr>
                    <td>{{ $rev->revision_no }}</td>
                    <td>{{ $rev->publish_date }}</td>
                    <td>
                        <a href="{{ Storage::url($rev->file_path) }}" target="_blank">İndir</a>
                    </td>
                    <td>{{ $rev->created_at }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Bu dokümanın eski revizyonu bulunmamaktadır.</p>
    @endif

    <a href="{{ route('external-documents.index') }}" class="btn btn-secondary mt-3">Geri Dön</a>
    <a href="{{ route('external-documents.edit',$doc->id) }}" class="btn btn-warning mt-3">Düzenle</a>
</div>
@endsection