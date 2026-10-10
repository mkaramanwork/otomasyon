@extends('layouts.admin')

@section('title','Hammadde SDS Yönetimi')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <h4 class="mb-0">
                🧪 Hammadde SDS Yönetimi
            </h4>

            <small class="text-muted">
                Mevcut hammaddelere ait Güvenlik Bilgi Formu kayıtları
            </small>

        </div>

        <div class="d-flex gap-2 flex-wrap">

                <a href="{{ route('sds.regulatory.index') }}"
                class="btn btn-dark">

                    🌐 Mevzuat Veritabanı

                </a>

                <a href="{{ route('sds.regulatory.import') }}"
                class="btn btn-primary">

                    🔄 Mevzuat Güncelleme

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

        <div class="alert alert-danger alert-dismissible fade show">

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

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    <div class="card mb-3">

        <div class="card-header">

            <strong>
                🔎 Hammadde Ara
            </strong>

        </div>

        <div class="card-body">

            <form method="GET"
                  action="{{ route('sds.raw-materials.index') }}">

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
                                   placeholder="Hammadde adı ara...">

                        </div>

                    </div>

                    <div class="col-md-3">

                        <div class="d-flex gap-2">

                            <button type="submit"
                                    class="btn btn-primary w-100">

                                Ara

                            </button>

                            <a href="{{ route('sds.raw-materials.index') }}"
                               class="btn btn-outline-secondary w-100">

                                Temizle

                            </a>

                        </div>

                    </div>

                </div>

                <div class="mt-2">

                    <small class="text-muted">

                        Toplam kayıt:

                        <strong>
                            {{ $rawMaterials->total() }}
                        </strong>

                    </small>

                </div>

            </form>

        </div>

    </div>


    <div class="card">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-striped table-hover table-bordered mb-0">

                    <thead class="table-dark">

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                Hammadde
                            </th>

                            <th>
                                Birim
                            </th>

                            <th>
                                SDS Durumu
                            </th>

                            <th>
                                Tedarikçi
                            </th>

                            <th>
                                SDS Tarihi
                            </th>

                            <th>
                                Revizyon
                            </th>

                            <th>
                                Dil
                            </th>

                            <th style="min-width:220px;">
                                İşlemler
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($rawMaterials as $rawMaterial)

                            @php
                                $sds = $rawMaterial->currentSdsProfile;
                            @endphp

                            <tr>

                                <td>
                                    {{ $rawMaterial->id }}
                                </td>

                                <td>

                                    <strong>
                                        {{ $rawMaterial->name }}
                                    </strong>

                                </td>

                                <td>

                                    {{ $rawMaterial->unit ?? '-' }}

                                </td>

                                <td>

                                    @if(!$sds)

                                        <span class="badge text-bg-secondary">
                                            SDS Yok
                                        </span>

                                    @elseif($sds->is_verified)

                                        <span class="badge text-bg-success">
                                            ✓ Doğrulandı
                                        </span>

                                    @else

                                        <span class="badge text-bg-warning">
                                            ⚠ Kontrol Bekliyor
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    {{ $sds?->supplier_name ?? '-' }}

                                </td>

                                <td>

                                    @if($sds?->supplier_sds_date)

                                        {{ $sds->supplier_sds_date->format('d.m.Y') }}

                                    @else

                                        -

                                    @endif

                                </td>

                                <td>

                                    {{ $sds?->supplier_sds_revision
                                        ?? $sds?->supplier_sds_version
                                        ?? '-' }}

                                </td>

                                <td>

                                    @if($sds)

                                        <span class="badge text-bg-dark">
                                            {{ strtoupper($sds->sds_language ?? '-') }}
                                        </span>

                                    @else

                                        -

                                    @endif

                                </td>

                                <td>

                                    <div class="d-flex gap-1 flex-wrap">

                                        @if($sds)

                                            <a href="{{ route('sds.raw-materials.show', [
                                                    $rawMaterial->id,
                                                    $sds->id
                                                ]) }}"
                                            class="btn btn-info btn-sm">

                                                🔍 SDS Detay

                                            </a>

                                            <a href="{{ route('sds.raw-materials.edit', [$rawMaterial->id, $sds->id]) }}" class="btn btn-warning btn-sm">✏️ Yönet</a>
                                            <a href="{{ route('sds.raw-materials.history', $rawMaterial->id) }}" class="btn btn-outline-secondary btn-sm">🕘 Geçmiş</a>

                                        @else

                                            <a href="{{ route('sds.raw-materials.create', $rawMaterial->id) }}"
                                               class="btn btn-success btn-sm">

                                                ➕ SDS Oluştur

                                            </a>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="9"
                                    class="text-center text-muted py-4">

                                    Hammadde kaydı bulunamadı.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        @if($rawMaterials->hasPages())

            <div class="card-footer">

                {{ $rawMaterials->links() }}

            </div>

        @endif

    </div>

</div>

@endsection