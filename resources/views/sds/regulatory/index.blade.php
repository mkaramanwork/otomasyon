@extends('layouts.admin')

@section('title', 'Mevzuat Veritabanı')

@section('content')

<style>
    .regulatory-table {
        width: 100%;
        margin-bottom: 0;
    }

    .regulatory-table th {
        white-space: nowrap;
        vertical-align: middle;
    }

    .regulatory-table td {
        vertical-align: middle;
    }

    .regulatory-name {
        min-width: 280px;
        max-width: 520px;
        white-space: normal;
        overflow-wrap: anywhere;
        word-break: break-word;
        line-height: 1.35;
    }

    .regulatory-identifier {
        white-space: nowrap;
    }

    .regulatory-source {
        max-width: 180px;
        white-space: normal;
        overflow-wrap: anywhere;
    }

    .regulatory-pagination .pagination {
        margin-bottom: 0;
        flex-wrap: wrap;
        justify-content: center;
    }

    .regulatory-pagination .page-link {
        min-width: 38px;
        text-align: center;
    }

    @media (max-width: 768px) {
        .regulatory-name {
            min-width: 220px;
            max-width: 320px;
        }
    }
</style>


<div class="container-fluid">

    <div class="d-flex flex-column flex-md-row
                justify-content-between
                align-items-md-center
                gap-2
                mb-3">

        <div>

            <h4 class="mb-0">
                🌐 Mevzuat Veritabanı
            </h4>

            <small class="text-muted">
                Kimyasal madde ve mevzuat kayıtlarının yönetimi
            </small>

        </div>

        <div class="d-flex flex-wrap gap-2">

            <a href="{{ route('sds.raw-materials.index') }}"
               class="btn btn-outline-secondary">

                ← SDS Yönetimi

            </a>

            <a href="{{ route('sds.regulatory.create') }}"
               class="btn btn-success">

                ➕ Kimyasal Ekle

            </a>

        </div>

    </div>


    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    @if($errors->any())

        <div class="alert alert-danger">

            <strong>
                İşlem gerçekleştirilemedi.
            </strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    <div class="card mb-3">

        <div class="card-header">

            <strong>
                🔎 Mevzuat Maddesi Ara
            </strong>

        </div>

        <div class="card-body">

            <form method="GET"
                  action="{{ route('sds.regulatory.index') }}">

                <div class="row g-2">

                    <div class="col-md-9">

                        <div class="input-group">

                            <span class="input-group-text">
                                🔎
                            </span>

                            <input type="text"
                                   name="q"
                                   class="form-control"
                                   value="{{ $search }}"
                                   placeholder="Kimyasal adı, CAS No, EC No veya Index No ara...">

                        </div>

                    </div>


                    <div class="col-md-3">

                        <div class="d-flex gap-2">

                            <button type="submit"
                                    class="btn btn-primary w-100">

                                Ara

                            </button>

                            <a href="{{ route('sds.regulatory.index') }}"
                               class="btn btn-outline-secondary w-100">

                                Temizle

                            </a>

                        </div>

                    </div>

                </div>


                <div class="mt-2">

                    <small class="text-muted">

                        Toplam mevzuat maddesi:

                        <strong>
                            {{ number_format($substances->total(), 0, ',', '.') }}
                        </strong>

                    </small>

                </div>

            </form>

        </div>

    </div>


    <div class="card">

        <div class="card-header bg-dark text-white">

            <div class="d-flex
                        justify-content-between
                        align-items-center">

                <strong>
                    Kimyasal Maddeler
                </strong>

                <span class="badge text-bg-light">

                    {{ number_format($substances->total(), 0, ',', '.') }}
                    kayıt

                </span>

            </div>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table
                              table-striped
                              table-hover
                              table-bordered
                              align-middle
                              regulatory-table">

                    <thead class="table-light">

                        <tr>

                            <th style="width:70px;">
                                #
                            </th>

                            <th style="min-width:280px;">
                                Kimyasal Madde
                            </th>

                            <th>
                                CAS No
                            </th>

                            <th>
                                EC No
                            </th>

                            <th>
                                Index No
                            </th>

                            <th>
                                Kaynak
                            </th>

                            <th>
                                Doğrulama
                            </th>

                            <th>
                                Son ECHA Kontrolü
                            </th>

                            <th>
                                Durum
                            </th>

                            <th style="min-width:100px;">
                                İşlem
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($substances as $substance)

                            <tr>

                                <td>
                                    {{ $substance->id }}
                                </td>


                                <td>

                                    <div class="regulatory-name"
                                         title="{{ $substance->preferred_name ?? '' }}">

                                        <strong>
                                            {{ $substance->preferred_name ?? '-' }}
                                        </strong>

                                    </div>

                                </td>


                                <td class="regulatory-identifier">

                                    @if($substance->cas_no)

                                        <span class="badge
                                                     text-bg-light
                                                     border
                                                     text-dark">

                                            {{ $substance->cas_no }}

                                        </span>

                                    @else

                                        <span class="text-muted">
                                            -
                                        </span>

                                    @endif

                                </td>


                                <td class="regulatory-identifier">

                                    {{ $substance->ec_no ?? '-' }}

                                </td>


                                <td class="regulatory-identifier">

                                    {{ $substance->index_no ?? '-' }}

                                </td>


                                <td>

                                    @if($substance->created_from)

                                        <span class="badge
                                                     text-bg-secondary
                                                     regulatory-source">

                                            {{ $substance->created_from }}

                                        </span>

                                    @else

                                        <span class="text-muted">
                                            -
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    @if($substance->verification_status === 'verified')

                                        <span class="badge text-bg-success">
                                            ✓ Doğrulandı
                                        </span>

                                    @elseif($substance->verification_status === 'review_required')

                                        <span class="badge text-bg-warning">
                                            ⚠ Kontrol Gerekli
                                        </span>

                                    @elseif($substance->verification_status === 'reference')

                                        <span class="badge text-bg-info">
                                            Referans
                                        </span>

                                    @else

                                        <span class="badge text-bg-secondary">
                                            Doğrulanmadı
                                        </span>

                                    @endif

                                </td>


                                <td class="regulatory-identifier">

                                    @if($substance->last_echa_check_at)

                                        {{ \Carbon\Carbon::parse(
                                            $substance->last_echa_check_at
                                        )->format('d.m.Y H:i') }}

                                    @else

                                        <span class="text-muted">
                                            -
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    @if($substance->is_active)

                                        <span class="badge text-bg-success">
                                            Aktif
                                        </span>

                                    @else

                                        <span class="badge text-bg-danger">
                                            Pasif
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    <a href="{{ route(
                                        'sds.regulatory.show',
                                        $substance->id
                                    ) }}"
                                       class="btn btn-info btn-sm">

                                        🔍 Detay

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="10"
                                    class="text-center py-5">

                                    <div class="mb-2">

                                        <strong>
                                            Mevzuat veritabanında henüz kayıt bulunmuyor.
                                        </strong>

                                    </div>

                                    <div class="text-muted mb-3">

                                        İlk kimyasal maddeyi CAS numarası ile
                                        sisteme ekleyebilirsiniz.

                                    </div>

                                    <a href="{{ route('sds.regulatory.create') }}"
                                       class="btn btn-success">

                                        ➕ İlk Kimyasalı Ekle

                                    </a>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        @if($substances->hasPages())

            <div class="card-footer bg-white">

                <div class="d-flex
                            flex-column
                            flex-lg-row
                            justify-content-between
                            align-items-center
                            gap-3">

                    <div class="text-muted small">

                        Toplam

                        <strong>
                            {{ number_format($substances->total(), 0, ',', '.') }}
                        </strong>

                        kayıttan

                        <strong>
                            {{ number_format($substances->firstItem(), 0, ',', '.') }}
                        </strong>

                        -

                        <strong>
                            {{ number_format($substances->lastItem(), 0, ',', '.') }}
                        </strong>

                        arası gösteriliyor.

                    </div>


                    <div class="regulatory-pagination">

                        {{ $substances
                            ->onEachSide(1)
                            ->links('pagination::bootstrap-5') }}

                    </div>

                </div>

            </div>

        @endif

    </div>

</div>

@endsection