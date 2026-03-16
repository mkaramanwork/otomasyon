@extends('layouts.admin')

@section('content')
<div class="container">
    <h4>Dış Kaynaklı Dokümanlar</h4>

    <!-- Yeni doküman ekleme -->
    <a href="{{ route('external-documents.create') }}" class="btn btn-primary mb-3">Yeni Doküman</a>

    <!-- Listeye Aktar butonu -->
    <a href="{{ route('external-documents.exportWord') }}" class="btn btn-success mb-3">
        Listeye Aktar (Word)
    </a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Sıra No</th>
                <th>Kod</th>
                <th>Başlık</th>
                <th>Kurum</th>
                <th>Revizyon</th>
                <th>Yayın Tarihi</th>
                <th>Gözden Geçirilme Tarihi</th>
                <th>URL</th>
                <th>Dosya</th>
                <th>Kayıt Eden</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            @foreach($docs as $doc)
            <tr>
                <td>{{ $doc->sequence_no }}</td>
                <td>{{ $doc->document_code }}</td>
                <td>{{ $doc->title }}</td>
                <td>{{ $doc->institution }}</td>
                <td>{{ $doc->revision_no }}</td>
                <td>{{ $doc->publish_date }}</td>
                <td>{{ $doc->review_date }}</td>
                <td>{{ $doc->url }}</td>
                <td>
                    <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                        Görüntüle
                    </a>
                </td>
                <td>{{ $doc->user->name ?? '-' }}</td>
                <td>
                    <a href="{{ route('external-documents.show',$doc->id) }}" class="btn btn-info btn-sm">Detay</a>
                    <a href="{{ route('external-documents.edit',$doc->id) }}" class="btn btn-warning btn-sm">Düzenle</a>
                    <form action="{{ route('external-documents.destroy',$doc->id) }}" method="POST" style="display:inline-block;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Silmek istediğine emin misin?')">
                            Sil
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection