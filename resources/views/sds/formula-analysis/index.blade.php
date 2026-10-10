@extends('layouts.admin')

@section('title', 'SDS Formül Analizi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1">🧪 SDS Formül Analizi</h3>
            <div class="text-muted">
                Formülden hammadde SDS bileşenlerini ve mevzuat verilerini toplayarak
                efektif konsantrasyon kontrolü yapar.
            </div>
        </div>

        <a href="{{ route('sds.raw-materials.index') }}"
           class="btn btn-outline-secondary">
            ← SDS Yönetimi
        </a>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <strong>Formül Ara</strong>
        </div>

        <div class="card-body">
            <form method="GET"
                  action="{{ route('sds.formula-analysis.index') }}">
                <div class="row g-2">
                    <div class="col-md-9">
                        <input type="text"
                               name="q"
                               class="form-control"
                               value="{{ $search }}"
                               placeholder="Formül adı veya Formula ID ara...">
                    </div>

                    <div class="col-md-2 d-grid">
                        <button type="submit"
                                class="btn btn-primary">
                            Ara
                        </button>
                    </div>

                    <div class="col-md-1 d-grid">
                        <a href="{{ route('sds.formula-analysis.index') }}"
                           class="btn btn-outline-secondary">
                            Temizle
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-dark text-white">
            <strong>Formüller</strong>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width:100px;">ID</th>
                            <th>Formül Adı</th>
                            <th style="width:180px;">Tip</th>
                            <th style="width:260px;">İşlem</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($formulas as $formula)
                        <tr>
                            <td>{{ $formula->id }}</td>
                            <td>
                                <strong>{{ $formula->name }}</strong>
                            </td>
                            <td>{{ $formula->type ?? '-' }}</td>
                            <td>
                                <a href="{{ route('sds.formula-analysis.show', [
                                        'formula' => $formula->id,
                                        'regulation_profile' => 'TR_KKDIK_SEA'
                                    ]) }}"
                                   class="btn btn-sm btn-success">
                                    TR Analiz Et
                                </a>

                                <a href="{{ route('sds.formula-analysis.show', [
                                        'formula' => $formula->id,
                                        'regulation_profile' => 'EU_REACH_CLP'
                                    ]) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    EU Analiz Et
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4"
                                class="text-center text-muted py-4">
                                Formül bulunamadı.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($formulas->hasPages())
            <div class="card-footer">
                {{ $formulas->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
