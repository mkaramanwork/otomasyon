@extends('layouts.admin')
@section('title','Doküman Yönetimi')
@section('content')
<div class="container">
    <h4>📑 Doküman Listesi</h4>
    <a href="{{ route('documents.create') }}" class="btn btn-primary mb-3">➕ Doküman Ekle</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Adı</th>
                <th>Revizyon</th>
                <th>Yayın</th>
                <th>Son Revizyon</th>
                <th>Gözden Geçirme</th>
                <th>Durum</th>
                <th>Güncel Dosya</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @forelse($documents as $doc)
            <tr>
                <td>{{ $doc->doc_no }}</td>
                <td>{{ $doc->title }}</td>
                <td>R{{ $doc->revision_no }}</td>
                <td>{{ $doc->publish_date }}</td>
                <td>{{ $doc->last_revision_date }}</td>
                <td>{{ $doc->review_date }}</td>
                <td>
                    <span class="badge bg-{{ $doc->status == 'guncel' ? 'success' : 'secondary' }}">
                        {{ ucfirst($doc->status) }}
                    </span>
                </td>
                <td>
                    @if($doc->latestRevision)
                        <a href="{{ asset('storage/'.$doc->latestRevision->file_path) }}" 
                           target="_blank" class="btn btn-sm btn-outline-primary">
                           Güncel Dosya
                        </a>
                    @else
                        <span class="text-muted">Dosya yok</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('documents.show',$doc->id) }}" class="btn btn-info btn-sm">Detay</a>
                    <a href="{{ route('documents.edit',$doc->id) }}" class="btn btn-warning btn-sm">Güncelle</a>
                    <form action="{{ route('documents.destroy',$doc->id) }}" method="POST" style="display:inline;" 
                          onsubmit="return confirm('Bu dokümanı silmek istediğine emin misin?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center">Henüz doküman yok</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection