@extends('layouts.admin')

@section('title','Müşteri Şikayet / Öneri Modülü')

@section('content')
<div class="container mt-4">
    <h3>📑 Müşteri Şikayet / Öneri Modülü</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="accordion" id="complaintAccordion">

        {{-- Şikayet Kaydı --}}
        <div class="accordion-item mb-3">
            <h2 class="accordion-header" id="headingCreate">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCreate">
                    ✍️ Yeni Şikayet / Öneri Kaydı
                </button>
            </h2>
            <div id="collapseCreate" class="accordion-collapse collapse" data-bs-parent="#complaintAccordion">
                <div class="accordion-body">
                    <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label>Şikayet Konusu</label>
                            <input type="text" name="subject" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Tanım</label>
                            <textarea name="description" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Sahibi (Firma/Kişi)</label>
                            <input type="text" name="owner" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Telefon</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Şikayeti Alan</label>
                            <input type="text" name="received_by" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Ürün Seçimi</label>
                            <select name="product_id" class="form-control">
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Dosya Ekle (Görsel/PDF)</label>
                            <input type="file" name="files[]" multiple class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Şikayet Listesi --}}
        <div class="accordion-item mb-3">
            <h2 class="accordion-header" id="headingList">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseList">
                    📋 Kayıtlı Şikayetler
                </button>
            </h2>
            <div id="collapseList" class="accordion-collapse collapse" data-bs-parent="#complaintAccordion">
                <div class="accordion-body">
                    <table class="table mt-3">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Konu</th>
                                <th>Ürün</th>
                                <th>Sahibi</th>
                                <th>Durum</th>
                                <th>Kayıt Eden</th>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($complaints as $c)
                                <tr>
                                    <td>{{ $c->complaint_no }}</td>
                                    <td>{{ $c->subject }}</td>
                                    <td>{{ $c->product->name }}</td>
                                    <td>{{ $c->owner }}</td>
                                    <td>{{ $c->status }}</td>
                                    <td>{{ $c->user->name }}</td>
                                    <td>
                                        <a href="{{ route('complaints.show',$c->id) }}" class="btn btn-info btn-sm">Detay</a>
                                        <a href="{{ route('complaints.edit',$c->id) }}" class="btn btn-warning btn-sm">Düzenle</a>
                                        <a href="{{ route('complaints.exportWord',$c->id) }}" class="btn btn-success btn-sm">Word</a>
                                        <form action="{{ route('complaints.destroy',$c->id) }}" method="POST" style="display:inline-block;" 
                                            onsubmit="return confirm('Bu şikayeti silmek istediğine emin misin?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                                        </form>
                                    </td>


                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection