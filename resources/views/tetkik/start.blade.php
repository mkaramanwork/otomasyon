@extends('layouts.admin')

@section('content')
<div class="container">
  <h4>{{ $plan->tetkik_no }} - Tetkik Soruları</h4>

  <form action="{{ route('tetkik.save',$plan->id) }}" method="POST">
    @csrf

    <div class="accordion" id="tetkikAccordion">
      @foreach($plan->sorular as $soru)
      <div class="accordion-item mb-2">
        <h2 class="accordion-header" id="heading-{{ $soru->id }}">
          <button class="accordion-button collapsed" type="button" 
                  data-bs-toggle="collapse" 
                  data-bs-target="#collapse-{{ $soru->id }}" 
                  aria-expanded="false" 
                  aria-controls="collapse-{{ $soru->id }}">
            {{ $loop->iteration }}. {{ $soru->standart_maddesi }} - {{ $soru->soru }}
          </button>
        </h2>
        <div id="collapse-{{ $soru->id }}" class="accordion-collapse collapse" 
             aria-labelledby="heading-{{ $soru->id }}" 
             data-bs-parent="#tetkikAccordion">
          <div class="accordion-body">
            
            {{-- Delil --}}
            <label class="fw-bold">Delil</label>
            <textarea name="delil[{{ $soru->id }}]" 
                      class="form-control form-control-lg mb-3" 
                      rows="3">{{ old("delil.$soru->id", $soru->delil) }}</textarea>

            {{-- Uygunsuzluk --}}
            <label class="fw-bold">Uygunsuzluk</label>
            <select name="uygunsuzluk_var[{{ $soru->id }}]" 
                    class="form-control form-control-lg uygunsuzluk-select mb-3" 
                    data-target="aciklama-{{ $soru->id }}">
              <option value="0" {{ $soru->uygunsuzluk_var == 0 ? 'selected' : '' }}>Yok</option>
              <option value="1" {{ $soru->uygunsuzluk_var == 1 ? 'selected' : '' }}>Var</option>
              <option value="2" {{ $soru->uygunsuzluk_var == 2 ? 'selected' : '' }}>Gözlem</option>
            </select>

            {{-- Açıklama --}}
            <label class="fw-bold">Açıklama</label>
            <textarea id="aciklama-{{ $soru->id }}" 
                      name="uygunsuzluk_aciklama[{{ $soru->id }}]" 
                      class="form-control form-control-lg {{ ($soru->uygunsuzluk_var == 1 || $soru->uygunsuzluk_var == 2) ? '' : 'd-none' }}" 
                      rows="4">{{ old("uygunsuzluk_aciklama.$soru->id", $soru->uygunsuzluk_aciklama) }}</textarea>

          </div>
        </div>
      </div>
      @endforeach
    </div>

    <button type="submit" class="btn btn-primary mt-3">Kaydet</button>
  </form>
</div>

{{-- JS ile alan aç/kapat --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.uygunsuzluk-select').forEach(function(select) {
        select.addEventListener('change', function() {
            const targetId = this.getAttribute('data-target');
            const textarea = document.getElementById(targetId);
            if (this.value === '1' || this.value === '2') {
                textarea.classList.remove('d-none');
            } else {
                textarea.classList.add('d-none');
                textarea.value = ''; // temizle
            }
        });
    });
});
</script>
@endsection