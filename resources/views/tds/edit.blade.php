@extends('layouts.admin')

@section('title','TDS Düzenle')

@section('content')
<div class="container">

    <h4>✏️ TDS Düzenle</h4>

    <form action="{{ route('tds.update',$doc->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3">

            <div class="col-md-4">
                <label>Ürün</label>
                <select name="product_id" class="form-select" required>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" @if($doc->product_id==$p->id) selected @endif>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label>Ürün Grubu</label>
                <select name="product_group" class="form-select" required>
                    <option value="Binek Araç" @if($doc->product_group=="Binek Araç") selected @endif>Binek Araç</option>
                    <option value="Ağır Hizmet" @if($doc->product_group=="Ağır Hizmet") selected @endif>Ağır Hizmet</option>
                    <option value="Endüstriyel" @if($doc->product_group=="Endüstriyel") selected @endif>Endüstriyel</option>
                    <option value="Antifriz" @if($doc->product_group=="Antifriz") selected @endif>Antifriz</option>
                    <option value="Gres" @if($doc->product_group=="Gres") selected @endif>Gres</option>
                    <option value="AdBlue" @if($doc->product_group=="AdBlue") selected @endif>AdBlue</option>
                </select>
            </div>

            <div class="col-md-4">
                <label>Ürün Kodu</label>
                <input type="text" name="product_code" class="form-control" value="{{ $doc->product_code }}">
            </div>

            <div class="col-md-6">
                <label>Ürün Adı</label>
                <input type="text" name="product_name" class="form-control" value="{{ $doc->product_name }}">
            </div>

            <div class="col-md-12">
                <label>Açıklama</label>
                <textarea name="description" class="form-control" rows="3">{{ $doc->description }}</textarea>
            </div>

            <div class="col-md-12 mt-3">
                <label>Onaylar</label>
                <textarea name="approvals" class="form-control" rows="2">{{ $doc->approvals }}</textarea>
            </div>

            {{-- ✅ Özellikler --}}
            <div class="col-md-12">
                <label>Özellikler</label>

                <div id="featureContainer">
                    @foreach($doc->features ?? [] as $i => $f)
                    <div class="row mb-2 feature-row">
                        <div class="col-md-5">
                            <input type="text" name="features[{{ $i }}][title]" class="form-control" value="{{ $f['title'] ?? '' }}">
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="features[{{ $i }}][value]" class="form-control" value="{{ $f['value'] ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger removeFeature">Sil</button>
                        </div>
                    </div>
                    @endforeach
                </div>

                <button type="button" id="addFeature" class="btn btn-secondary mt-2">Özellik Ekle</button>
            </div>

            {{-- ✅ Parametreler --}}
            <div class="col-md-12 mt-4">
                <label>Parametreler</label>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Seç</th>
                            <th>Parametre</th>
                            <th>Birim</th>
                            <th>Metot</th>
                            <th>Değer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($params as $p)
                        <tr>
                            <td>
                                <input type="checkbox" name="param_select[{{ $p->id }}]" 
                                    @if(isset($doc->parameters) && collect($doc->parameters)->pluck('id')->contains($p->id)) checked @endif>
                            </td>
                            <td>{{ $p->parameter }}</td>
                            <td>{{ $p->unit }}</td>
                            <td>{{ $p->method_tr }}</td>
                            <td>
                                <input type="text" name="param_value[{{ $p->id }}]" class="form-control"
                                       value="{{ collect($doc->parameters)->firstWhere('id',$p->id)['value'] ?? '' }}">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

        <button class="btn btn-primary mt-3">Güncelle</button>
    </form>

</div>

<script>
let index = {{ count($doc->features ?? []) }};

document.getElementById('addFeature').addEventListener('click', function () {
    let container = document.getElementById('featureContainer');
    let html = `
        <div class="row mb-2 feature-row">
            <div class="col-md-5">
                <input type="text" name="features[${index}][title]" class="form-control" placeholder="Özellik Adı">
            </div>
            <div class="col-md-5">
                <input type="text" name="features[${index}][value]" class="form-control" placeholder="Değer">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger removeFeature">Sil</button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    index++;
});

document.addEventListener('click', function(e){
    if(e.target.classList.contains('removeFeature')){
        e.target.closest('.feature-row').remove();
    }
});
</script>
@endsection