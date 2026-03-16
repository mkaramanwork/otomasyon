@extends('layouts.admin')

@section('title','Ürün Sicil Kartları')

@section('content')
<div class="container mt-4">

    <!-- Başarılı mesaj -->
    @if(session('success'))
        <div class="alert alert-success">{!! session('success') !!}</div>
        @if(session('product_id'))
            <script>
                setTimeout(function() {
                    window.location.href = "{{ route('urunAgaci.show', session('product_id')) }}";
                }, 1000);
            </script>
        @endif
    @endif

    <!-- Geri Dön Butonu -->
<div class="mb-3">
    <a href="{{ route('urunAgaci.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Sicil Kartı Listesine Dön
    </a>

    <a href="{{ route('urunAgaci.sicilCard', $product->id) }}" class="btn btn-primary ms-2">
        <i class="fas fa-file-word"></i> Sicil Kartı Export
    </a>
</div>


    <h3>🔎 Ürün Sicil Kartı: {{ $product->name }} (Kod: {{ $product->code }})</h3>

    <div class="accordion" id="urunAccordion">

        <!-- Kimyasal Formüller -->
        <div class="accordion-item mb-3">
            <h2 class="accordion-header" id="headingFormula">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFormula">
                    ⚗️ Kimyasal Formüller
                </button>
            </h2>
            <div id="collapseFormula" class="accordion-collapse collapse" data-bs-parent="#urunAccordion">
                <div class="accordion-body">
                    <a href="{{ route('formulas.create', ['product_id' => $product->id]) }}" class="btn btn-success mb-3">➕ Formül Ekle</a>

                    @if($product->formulas->isEmpty())
                        <div class="alert alert-info">Henüz formül eklenmemiş.</div>
                    @else
                        @foreach($product->formulas as $index => $formula)
                            <div class="card mb-3">
                                <div class="card-header">
                                    <strong>{{ $index + 1 }}. {{ $formula->name }}</strong>
                                    <span class="text-muted">({{ $formula->type }})</span>
                                </div>
                                <div class="card-body">
                                    <p>{{ $formula->description ?? 'Açıklama yok' }}</p>
                                    <p><small>Ekleyen: {{ $formula->addedBy->name ?? '-' }} | Tarih: {{ $formula->created_at->format('d.m.Y H:i') }}</small></p>

                                    @if($formula->details->isEmpty())
                                        <div class="alert alert-warning">Bu formüle ait ham madde bulunamadı.</div>
                                        <a href="{{ route('formulas.edit', $formula->id) }}" class="btn btn-sm btn-primary">➕ Ham Madde Ekle</a>
                                    @else
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Ham Madde</th>
                                                    <th>Oran (%)</th>
                                                    <th>Birim</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($formula->details as $detail)
                                                    <tr>
                                                        <td>{{ $detail->rawMaterial->name }}</td>
                                                        <td>{{ $detail->percentage }}</td>
                                                        <td>{{ $detail->unit ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <a href="{{ route('formulas.edit', $formula->id) }}" class="btn btn-sm btn-primary">➕ Yeni Ham Madde Ekle</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <!-- Fiziksel Reçeteler -->
        <div class="accordion-item mb-3">
            <h2 class="accordion-header" id="headingPhysical">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePhysical">
                    ⚙️ Fiziksel Reçeteler
                </button>
            </h2>
            <div id="collapsePhysical" class="accordion-collapse collapse" data-bs-parent="#urunAccordion">
                <div class="accordion-body">
                    <a href="{{ route('physcial_formula.create', ['product_id' => $product->id]) }}" class="btn btn-success mb-3">➕ Fiziksel Reçete Ekle</a>

                    @if($product->physicalFormulas->isEmpty())
                        <div class="alert alert-info">Henüz fiziksel reçete eklenmemiş.</div>
                    @else
                        @foreach($product->physicalFormulas as $index => $formula)
                            <div class="card mb-3">
                                <div class="card-header">
                                    <strong>{{ $index + 1 }}. {{ $formula->product_name }}</strong>
                                    <span class="text-muted">({{ $formula->unit ?? '-' }})</span>
                                </div>
                                <div class="card-body">
                                    <p>{{ $formula->description ?? 'Açıklama yok' }}</p>
                                    <p><small>Tarih: {{ $formula->created_at->format('d.m.Y H:i') }}</small></p>

                                    @if($formula->details->isEmpty())
                                        <div class="alert alert-warning">Bu reçeteye ait parametre bulunamadı.</div>
                                        <a href="{{ route('physcial_formula.addDetail', $formula->id) }}" class="btn btn-sm btn-primary">➕ Parametre Ekle</a>
                                    @else
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Parametre</th>
                                                    <th>Seçim</th>
                                                    <th>Miktar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($formula->details as $detail)
                                                    <tr>
                                                        <td>{{ $detail->parameter->name }}</td>
                                                        <td>{{ $detail->selection_label }}</td>
                                                        <td>{{ $detail->quantity }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <a href="{{ route('physcial_formula.addDetail', $formula->id) }}" class="btn btn-sm btn-primary">➕ Yeni Parametre Ekle</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <!-- Barkodlar -->
        <div class="accordion-item mb-3">
            <h2 class="accordion-header" id="headingBarcode">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBarcode">
                    📦 Barkodlar
                </button>
            </h2>
            <div id="collapseBarcode" class="accordion-collapse collapse" data-bs-parent="#urunAccordion">
                <div class="accordion-body">

                    <!-- Barkod Ekle Butonu -->
                    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addBarcodeModal">
                        ➕ Barkod Ekle
                    </button>

                    @if($product->barcodes->isEmpty())
                        <div class="alert alert-info">Henüz barkod eklenmemiş.</div>
                    @else
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Barkod</th>
                                    <th>Açıklama</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($product->barcodes as $index => $barcode)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="text-center">
                                            <span id="barcode-{{ $barcode->id }}">{{ $barcode->code }}</span>
                                            <button class="btn btn-sm btn-outline-secondary ms-2" 
                                                    onclick="copyToClipboard('barcode-{{ $barcode->id }}')">
                                                📋
                                            </button>
                                        </td>
                                        <td>{{ $barcode->description ?? '-' }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editBarcodeModal{{ $barcode->id }}">
                                                Düzenle
                                            </button>

                                            <!-- Sil -->
                                            <form action="{{ route('barcodes.destroy', $barcode->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Silmek istediğinize emin misiniz?')">
                                                    Sil
                                                </button>
                                            </form>

                                            <!-- İndir -->
                                            <a href="{{ route('barcodes.download', $barcode->id) }}" class="btn btn-sm btn-info">
                                                ⬇️ İndir
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- Düzenleme Modal -->
                                    <div class="modal fade" id="editBarcodeModal{{ $barcode->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form method="POST" action="{{ route('barcodes.update', $barcode->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Barkod Düzenle</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Barkod Kodu</label>
                                                            <input type="text" name="code" class="form-control" value="{{ $barcode->code }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Açıklama</label>
                                                            <input type="text" name="description" class="form-control" value="{{ $barcode->description }}">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-success">Kaydet</button>
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                </div>
            </div>
        </div>

        <!-- Barkod Ekle Modal -->
        <div class="modal fade" id="addBarcodeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('barcodes.store') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Barkod Ekle</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Barkod Kodu</label>
                                <input type="text" name="code" class="form-control" placeholder="Örn: 8691234567890" required>
                                <small class="text-muted">⚠️ EAN-13 standardına göre barkod 13 haneli olmalıdır.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Açıklama</label>
                                <input type="text" name="description" class="form-control" placeholder="Avrupa barkodu, iç pazar barkodu...">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Kaydet</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        
<!-- Etiket Görselleri -->
<div class="accordion-item mb-3">
    <h2 class="accordion-header" id="headingLabels">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLabels">
            🏷️ Etiket Görselleri
        </button>
    </h2>
    <div id="collapseLabels" class="accordion-collapse collapse" data-bs-parent="#urunAccordion">
        <div class="accordion-body">
            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addLabelModal">➕ Etiket Görseli Ekle</button>

            @if($product->labels->isEmpty())
                <div class="alert alert-info">Henüz etiket görseli eklenmemiş.</div>
            @else
                <div class="row">
                    @foreach($product->labels as $label)
                        <div class="col-md-3 text-center mb-3">
                            @if(Str::endsWith($label->file_path, ['.png','.jpg','.jpeg']))
                                <img src="{{ asset('storage/'.$label->file_path) }}" class="img-thumbnail" alt="Etiket">
                            @elseif(Str::endsWith($label->file_path, '.pdf'))
                                <i class="bi bi-file-earmark-pdf" style="font-size:48px;color:red;"></i>
                            @endif

                            <p class="mt-2">{{ $label->name }} ({{ $label->type }})</p>
                            <p>{{ $label->description ?? '-' }}</p>

                            <a href="{{ route('labels.download', $label->id) }}" class="btn btn-sm btn-info">⬇️ İndir</a>
                            <form action="{{ route('labels.destroy', $label->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Sil</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Etiket Ekleme Modal -->
<div class="modal fade" id="addLabelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('labels.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Etiket Görseli Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Etiket Adı</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tip</label>
                        <select name="type" class="form-select" required>
                            <option value="ön">Ön</option>
                            <option value="arka">Arka</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dosya (PNG, JPG, JPEG, PDF)</label>
                        <input type="file" name="file" class="form-control" accept=".png,.jpg,.jpeg,.pdf" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Kaydet</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Koli Görselleri -->
<div class="accordion-item mb-3">
    <h2 class="accordion-header" id="headingBoxes">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBoxes">
            📦 Koli Görselleri
        </button>
    </h2>
    <div id="collapseBoxes" class="accordion-collapse collapse" data-bs-parent="#urunAccordion">
        <div class="accordion-body">
            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addBoxModal">➕ Koli Görseli Ekle</button>

            @if($product->packageBoxes->isEmpty())
                <div class="alert alert-info">Henüz koli görseli eklenmemiş.</div>
            @else
                <div class="row">
                    @foreach($product->packageBoxes as $box)
                        <div class="col-md-3 text-center mb-3">
                            @if(Str::endsWith($box->file_path, ['.png','.jpg','.jpeg']))
                                <img src="{{ asset('storage/'.$box->file_path) }}" class="img-thumbnail" alt="Koli">
                            @elseif(Str::endsWith($box->file_path, '.pdf'))
                                <i class="bi bi-file-earmark-pdf" style="font-size:48px;color:red;"></i>
                            @endif

                            <p class="mt-2">{{ $box->name }}</p>
                            <p>{{ $box->description ?? '-' }}</p>

                            <a href="{{ route('package-boxes.download', $box->id) }}" class="btn btn-sm btn-info">⬇️ İndir</a>
                            <form action="{{ route('package-boxes.destroy', $box->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Sil</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Koli Görseli Ekleme Modal -->
<div class="modal fade" id="addBoxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('package-boxes.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Koli Görseli Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Koli Adı</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dosya (PNG, JPG, JPEG, PDF)</label>
                        <input type="file" name="file" class="form-control" accept=".png,.jpg,.jpeg,.pdf" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Kaydet</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- Koli Görseli Ekleme Modal -->
<div class="accordion-item mb-3">
    <div class="modal fade" id="addBoxModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('package-boxes.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Koli Görseli Ekle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Koli Adı</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Açıklama</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dosya (PNG, JPG, JPEG, PDF)</label>
                            <input type="file" name="file" class="form-control" accept=".png,.jpg,.jpeg,.pdf" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Kaydet</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Kapaklar -->
<div class="accordion-item mb-3">
    <h2 class="accordion-header" id="headingCaps">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCaps">
            🧢 Kapaklar
        </button>
    </h2>
    <div id="collapseCaps" class="accordion-collapse collapse" data-bs-parent="#urunAccordion">
        <div class="accordion-body">
            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addCapModal">➕ Kapak Ekle</button>

            @if($product->caps->isEmpty())
                <div class="alert alert-info">Henüz kapak eklenmemiş.</div>
            @else
                <div class="row">
                    @foreach($product->caps as $cap)
                        <div class="col-md-3 text-center mb-3">
                            @if(Str::endsWith($cap->file_path, ['.png','.jpg','.jpeg']))
                                <img src="{{ asset('storage/'.$cap->file_path) }}" class="img-thumbnail" alt="Kapak">
                            @elseif(Str::endsWith($cap->file_path, '.pdf'))
                                <i class="bi bi-file-earmark-pdf" style="font-size:48px;color:red;"></i>
                            @endif

                            <p class="mt-2">{{ $cap->name }}</p>
                            <p>{{ $cap->description ?? '-' }}</p>

                            <a href="{{ route('caps.download', $cap->id) }}" class="btn btn-sm btn-info">⬇️ İndir</a>
                            <form action="{{ route('caps.destroy', $cap->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Sil</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Kapak Ekleme Modal -->
<div class="modal fade" id="addCapModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('caps.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kapak Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kapak Adı</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dosya (PNG, JPG, JPEG, PDF)</label>
                        <input type="file" name="file" class="form-control" accept=".png,.jpg,.jpeg,.pdf" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Kaydet</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Şişe / Ambalaj Görselleri -->
<div class="accordion-item mb-3">
    <h2 class="accordion-header" id="headingBottles">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBottles">
            🍶 Şişe / Ambalaj Görselleri
        </button>
    </h2>
    <div id="collapseBottles" class="accordion-collapse collapse" data-bs-parent="#urunAccordion">
        <div class="accordion-body">
            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addBottleModal">➕ Şişe / Ambalaj Ekle</button>

            @if($product->bottles->isEmpty())
                <div class="alert alert-info">Henüz şişe/ambalaj eklenmemiş.</div>
            @else
                <div class="row">
                    @foreach($product->bottles as $bottle)
                        <div class="col-md-3 text-center mb-3">
                            @if(Str::endsWith($bottle->file_path, ['.png','.jpg','.jpeg']))
                                <img src="{{ asset('storage/'.$bottle->file_path) }}" class="img-thumbnail" alt="Şişe">
                            @elseif(Str::endsWith($bottle->file_path, '.pdf'))
                                <i class="bi bi-file-earmark-pdf" style="font-size:48px;color:red;"></i>
                            @endif

                            <p class="mt-2">{{ $bottle->name }}</p>
                            <p>{{ $bottle->description ?? '-' }}</p>

                            <a href="{{ route('bottles.download', $bottle->id) }}" class="btn btn-sm btn-info">⬇️ İndir</a>
                            <form action="{{ route('bottles.destroy', $bottle->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Sil</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Şişe / Ambalaj Ekleme Modal -->
<div class="modal fade" id="addBottleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('bottles.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Şişe / Ambalaj Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Şişe / Ambalaj Adı</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dosya (PNG, JPG, JPEG, PDF)</label>
                        <input type="file" name="file" class="form-control" accept=".png,.jpg,.jpeg,.pdf" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Kaydet</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- TDS Dokümanları -->
<div class="accordion-item mb-3">
    <h2 class="accordion-header" id="headingTdsDocs">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTdsDocs">
            📄 TDS Dokümanları
        </button>
    </h2>
    <div id="collapseTdsDocs" class="accordion-collapse collapse" data-bs-parent="#urunAccordion">
        <div class="accordion-body">

            @if($product->tdsDocuments->isEmpty())
                <div class="alert alert-warning">
                    Bu ürüne ait TDS dokümanı bulunamadı.<br>
                    Lütfen <a href="{{ route('tds.create') }}" class="alert-link">TDS ekleme modülünden</a> ekleyiniz.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Ürün Adı</th>
                                <th>Ürün Kodu</th>
                                <th>Açıklama</th>
                                <th>Onaylar</th>
                                <th>Özellikler</th>
                                <th>Parametreler</th>
                                <th>Word Export</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product->tdsDocuments as $doc)
                                <tr>
                                    <td>{{ $doc->product_name }}</td>
                                    <td>{{ $doc->product_code }}</td>
                                    <td>{{ $doc->description }}</td>
                                    <td>{{ $doc->approvals ?? '-' }}</td>
                                    <td>
                                        @foreach($doc->features ?? [] as $f)
                                            • {{ $f['title'] ?? '' }}: {{ $f['value'] ?? '' }} <br>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($doc->parameters ?? [] as $p)
                                            {{ $p['parameter'] }} ({{ $p['unit'] }}): {{ $p['value'] }} <br>
                                        @endforeach
                                    </td>
                                    <td>
                                        <a href="{{ route('tds.exportWord',$doc->id) }}" class="btn btn-sm btn-primary">
                                            Word Export
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </div>
</div>

</div> <!-- accordion end -->
<script>
function copyToClipboard(elementId) {
    var text = document.getElementById(elementId).innerText;
    navigator.clipboard.writeText(text).then(function() {
        alert("Barkod kopyalandı: " + text);
    });
}
</script>
</div> <!-- container end -->
@endsection