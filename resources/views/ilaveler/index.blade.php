@extends('layouts.admin')
@section('title','İlaveler')

@section('content')
<div class="container">
    <h4 class="mb-4">İlave Nedenler Listesi</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($additions->isEmpty())
        <div class="alert alert-info">Henüz ilave kaydı bulunmamaktadır.</div>
    @else
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Numune Adı</th>
                            <th>Batch No</th>
                            <th>Tank No</th>
                            <th>İlave Nedeni</th>
                            <th>Parametreler</th>
                            <th>Uygunsuz Sonuç</th>
                            <th>Düzenleme</th>
                            <th>İlave Tarihi</th>
                            <th>Kaydeden</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($additions as $addition)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $addition->sample_name }}</td>
                                <td>{{ $addition->batch_no }}</td>
                                <td>{{ $addition->tank_no ?? '-' }}</td>
                                <td>{{ $addition->reason }}</td>
                                <td>
                                    @if($addition->parameters)
                                        @foreach($addition->parameters as $p)
                                            @php
                                                $param = \App\Models\LabParameter::find($p);
                                            @endphp
                                            <span class="badge bg-info">
                                                {{ $param->parameter ?? $param->name ?? $p }}
                                            </span>
                                        @endforeach
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $addition->nonconformity_result ?? '-' }}</td>
                                <td>{{ $addition->nonconformity_fix ?? '-' }}</td>
                                <td>{{ $addition->reason_date?->format('d.m.Y H:i') ?? '-' }}</td>
                                <td>{{ $addition->user->name ?? '-' }}</td>
                                <td>
                                    <form action="{{ route('additions.destroyAddition', $addition->id) }}" method="POST" onsubmit="return confirm('Bu kaydı silmek istediğine emin misin?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Sil
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $additions->links() }}
                </div>
            </div>
        </div>
    @endif
</div>
@endsection