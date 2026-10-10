@extends('layouts.admin')

@section('title','TDS Listesi')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>

            <h4 class="mb-0">
                📑 TDS Listesi
            </h4>

            <small class="text-muted">
                Teknik Bilgi Formları
            </small>

        </div>


        <div class="d-flex gap-2 flex-wrap">

            <a href="{{ route('tds.create') }}"
               class="btn btn-success">

                ➕ Yeni TDS

            </a>


            <a href="{{ route('tds.clone') }}"
               class="btn btn-dark">

                📄 TDS Klonla

            </a>


            <button type="button"
                    class="btn btn-dark"
                    data-bs-toggle="modal"
                    data-bs-target="#brandModal">

                🏷️ Marka Yönetimi

            </button>


            <a href="{{ route('tds.printAll') }}"
               class="btn btn-primary">

                🖨️ Tüm TDS’leri Yazdır

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
                🔎 TDS Ara
            </strong>

        </div>


        <div class="card-body">

            <div class="row g-2">

                <div class="col-md-9">

                    <div class="input-group">

                        <span class="input-group-text">
                            🔎
                        </span>

                        <input type="text"
                               id="tdsSearch"
                               class="form-control"
                               placeholder="Marka, ürün adı, ürün kodu veya açıklama ara...">

                    </div>

                </div>


                <div class="col-md-3">

                    <button type="button"
                            id="clearSearch"
                            class="btn btn-outline-secondary w-100">

                        Temizle

                    </button>

                </div>

            </div>


            <div class="mt-2">

                <small class="text-muted">

                    Görünen kayıt:

                    <strong id="visibleCount">
                        {{ $documents->count() }}
                    </strong>

                    /

                    {{ $documents->count() }}

                </small>

            </div>

        </div>

    </div>


    <div class="card">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-striped table-hover table-bordered mb-0"
                       id="tdsTable">

                    <thead class="table-dark">

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                Marka
                            </th>

                            <th>
                                Ürün Adı
                            </th>

                            <th>
                                Ürün Kodu
                            </th>

                            <th>
                                Açıklama
                            </th>

                            <th>
                                Oluşturulma
                            </th>

                            <th style="min-width: 420px;">
                                İşlemler
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($documents as $doc)

                            <tr class="tds-row">

                                <td>
                                    {{ $doc->id }}
                                </td>


                                <td>

                                    @if($doc->brand)

                                        <span class="badge text-bg-dark">

                                            {{ $doc->brand->name }}

                                        </span>

                                    @else

                                        <span class="badge text-bg-secondary">

                                            Markasız

                                        </span>

                                    @endif

                                </td>


                                <td>

                                    {{ $doc->product_name }}

                                </td>


                                <td>

                                    {{ $doc->product_code }}

                                </td>


                                <td>

                                    {{ Str::limit($doc->description, 70) }}

                                </td>


                                <td>

                                    {{ $doc->created_at?->format('d.m.Y H:i') }}

                                </td>


                                <td>

                                    <div class="d-flex gap-1 flex-wrap">

                                        <a href="{{ route('tds.show',$doc->id) }}"
                                           class="btn btn-info btn-sm">

                                            🔍 Detay

                                        </a>


                                        <a href="{{ route('tds.edit',$doc->id) }}"
                                           class="btn btn-warning btn-sm">

                                            ✏️ Düzenle

                                        </a>


                                        <a href="{{ route('tds.exportWord',$doc->id) }}"
                                           class="btn btn-primary btn-sm">

                                            📥
                                            {{ $doc->brand?->name ?? 'TDS' }}
                                            Word

                                        </a>


                                        <form action="{{ route('tds.destroy',$doc->id) }}"
                                              method="POST">

                                            @csrf

                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Bu TDS kaydını silmek istediğine emin misin?')">

                                                🗑️ Sil

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7"
                                    class="text-center text-muted py-4">

                                    Henüz TDS kaydı bulunmuyor.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>



<div class="modal fade"
     id="brandModal"
     tabindex="-1"
     aria-labelledby="brandModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header bg-dark text-white">

                <h5 class="modal-title"
                    id="brandModalLabel">

                    🏷️ Marka Yönetimi

                </h5>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Kapat">
                </button>

            </div>


            <div class="modal-body">

                <form action="{{ route('brands.store') }}"
                      method="POST">

                    @csrf


                    <label class="form-label fw-bold">

                        Yeni Marka Ekle

                    </label>


                    <div class="mb-3">

                        <label class="form-label">

                            Marka Adı

                        </label>

                        <input type="text"
                               name="name"
                               class="form-control"
                               placeholder="Örn: AKSOIL, VOLLMAX, NYTRON"
                               value="{{ old('name') }}"
                               required>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">

                            TDS Taslak Dosyası

                        </label>

                        <div class="input-group">

                            <input type="text"
                                   name="template_file"
                                   class="form-control"
                                   placeholder="Örn: tds_aksoil.docx"
                                   value="{{ old('template_file') }}">

                            <button type="submit"
                                    class="btn btn-success">

                                ➕ Ekle

                            </button>

                        </div>

                        <small class="text-muted">

                            Word taslağının dosya adını .docx uzantısı ile yazınız.

                        </small>

                    </div>

                </form>


                <hr>


                <div class="d-flex justify-content-between align-items-center mb-2">

                    <h6 class="mb-0">

                        Mevcut Markalar

                    </h6>

                    <span class="badge text-bg-secondary">

                        {{ $brands->count() }} Marka

                    </span>

                </div>


                <div style="max-height:350px; overflow-y:auto;">

                    @forelse($brands as $brand)

                        <div class="d-flex
                                    justify-content-between
                                    align-items-center
                                    border
                                    rounded
                                    p-2
                                    mb-2">

                            <div>

                                <strong>

                                    {{ $brand->name }}

                                </strong>


                                <div class="mt-1">

                                    @if($brand->template_file)

                                        <small class="text-muted">

                                            Taslak:

                                            <span class="badge text-bg-success">

                                                {{ $brand->template_file }}

                                            </span>

                                        </small>

                                    @else

                                        <span class="badge text-bg-warning">

                                            Taslak Tanımlanmamış

                                        </span>

                                    @endif

                                </div>

                            </div>


                            <form action="{{ route('brands.destroy',$brand->id) }}"
                                  method="POST">

                                @csrf

                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('{{ $brand->name }} markasını silmek istediğine emin misin?')">

                                    🗑️ Sil

                                </button>

                            </form>

                        </div>

                    @empty

                        <div class="alert alert-light border text-center">

                            Henüz marka eklenmemiş.

                        </div>

                    @endforelse

                </div>

            </div>


            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                    Kapat

                </button>

            </div>

        </div>

    </div>

</div>



<script>

document.addEventListener('DOMContentLoaded', function () {

    const searchInput =
        document.getElementById('tdsSearch');

    const clearButton =
        document.getElementById('clearSearch');

    const rows =
        document.querySelectorAll(
            '#tdsTable tbody .tds-row'
        );

    const visibleCount =
        document.getElementById('visibleCount');


    function filterTable() {

        const searchValue =
            searchInput.value
                .toLocaleLowerCase('tr-TR')
                .trim();

        let count = 0;


        rows.forEach(function (row) {

            const rowText =
                row.innerText
                    .toLocaleLowerCase('tr-TR');


            if (rowText.includes(searchValue)) {

                row.style.display = '';

                count++;

            } else {

                row.style.display = 'none';

            }

        });


        visibleCount.textContent = count;

    }


    searchInput.addEventListener(
        'input',
        filterTable
    );


    clearButton.addEventListener(
        'click',
        function () {

            searchInput.value = '';

            filterTable();

            searchInput.focus();

        }
    );

});

</script>


@if($errors->any())

<script>

document.addEventListener('DOMContentLoaded', function () {

    const brandModalElement =
        document.getElementById('brandModal');


    if (
        brandModalElement &&
        typeof bootstrap !== 'undefined'
    ) {

        const brandModal =
            new bootstrap.Modal(
                brandModalElement
            );

        brandModal.show();

    }

});

</script>

@endif

@endsection