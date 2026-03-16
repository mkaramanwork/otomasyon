@extends('layouts.admin')
@section('title','Personel Detay')
@section('content')
<div class="container">
    <h4>📄 Personel Detay</h4>

    <ul>
        <li><strong>Adı:</strong> {{ $personnel->name }}</li>
        <li><strong>Ünvanı:</strong> {{ $personnel->title }}</li>
        <li><strong>Görevi:</strong> {{ $personnel->duty }}</li>
        <li><strong>İşe Başlama:</strong> {{ $personnel->start_date }}</li>
        <li><strong>Vekili:</strong> {{ $personnel->deputy }}</li>
    </ul>

    <hr>
    <h5>Oryantasyon Planları</h5>
    <!-- Oryantasyon Planı Modal Butonu -->
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#orientationModal">
        ➕ Oryantasyon Planı
    </button>
    <ul>
        @foreach($personnel->orientationPlans as $plan)
            <li>
                {{ $plan->department }} - {{ $plan->purpose }}
                @if($plan->is_completed) ✅ Tamamlandı @endif

                <!-- Faaliyet Modal Butonu -->
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#activityModal{{ $plan->id }}">
                    ➕ Faaliyet Ekle
                </button>

                <!-- Tamamlama Modal Butonu -->
                @if(!$plan->is_completed)
                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#completeModal{{ $plan->id }}">
                    ✔️ Tamamla
                </button>
                @endif

                <!-- Faaliyetler Listesi -->
                <ul>
                    @foreach($plan->activities as $act)
                        <li>{{ $act->activity }} ({{ $act->trainer }}) {{ $act->start_date }} - {{ $act->end_date }} Sonuç: {{ $act->result }}</li>
                    @endforeach
                </ul>

                <!-- Faaliyet Modal -->
                <div class="modal fade" id="activityModal{{ $plan->id }}" tabindex="-1" aria-labelledby="activityModalLabel{{ $plan->id }}" aria-hidden="true">
                  <div class="modal-dialog">
                    <form action="{{ route('activity.store',$plan->id) }}" method="POST">
                        @csrf
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="activityModalLabel{{ $plan->id }}">Faaliyet Ekle</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                              <div class="mb-3">
                                  <label>Faaliyet</label>
                                  <input type="text" name="activity" class="form-control" required>
                              </div>
                              <div class="mb-3">
                                  <label>Eğitimi Veren</label>
                                  <input type="text" name="trainer" class="form-control" required>
                              </div>
                              <div class="mb-3">
                                  <label>Başlangıç Tarihi</label>
                                  <input type="date" name="start_date" class="form-control" required>
                              </div>
                              <div class="mb-3">
                                  <label>Bitiş Tarihi</label>
                                  <input type="date" name="end_date" class="form-control" required>
                              </div>
                              <div class="mb-3">
                                  <label>Sonuç</label>
                                  <input type="text" name="result" class="form-control">
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

                <!-- Tamamlama Modal -->
                <div class="modal fade" id="completeModal{{ $plan->id }}" tabindex="-1" aria-labelledby="completeModalLabel{{ $plan->id }}" aria-hidden="true">
                  <div class="modal-dialog">
                    <form action="{{ route('orientation.complete',$plan->id) }}" method="POST">
                        @csrf
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="completeModalLabel{{ $plan->id }}">Oryantasyon Tamamlama</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                              <div class="mb-3">
                                  <label>Değerlendirme Tarihi</label>
                                  <input type="date" name="evaluation_date" class="form-control" required>
                              </div>
                              <div class="mb-3">
                                  <label>Onay Tarihi</label>
                                  <input type="date" name="approval_date" class="form-control" required>
                              </div>
                          </div>
                          <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Tamamla</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                          </div>
                        </div>
                    </form>
                  </div>
                </div>
            </li>
        @endforeach
    </ul>

    <!-- Oryantasyon Planı Modal -->
    <div class="modal fade" id="orientationModal" tabindex="-1" aria-labelledby="orientationModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form action="{{ route('orientation.store',$personnel->id) }}" method="POST">
            @csrf
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="orientationModalLabel">Oryantasyon Planı Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                  <div class="mb-3">
                      <label>Bölüm</label>
                      <input type="text" name="department" class="form-control" required>
                  </div>
                  <div class="mb-3">
                      <label>Oryantasyon Amacı</label>
                      <textarea name="purpose" class="form-control" required></textarea>
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

    <hr>
    <h5>Yetkinlikler</h5>
    <!-- Yetkinlik Modal Butonu -->
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#competencyModal">
        ➕ Yetkinlik Ata
    </button>
    <ul>
        @foreach($personnel->competencies as $c)
            <li>{{ $c->method }} (Seviye: {{ $c->pivot->level }}, Tarih: {{ $c->pivot->granted_at }})</li>
        @endforeach
    </ul>

    <!-- Yetkinlik Modal -->
    <div class="modal fade" id="competencyModal" tabindex="-1" aria-labelledby="competencyModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form action="{{ route('competency.assign',$personnel->id) }}" method="POST">
            @csrf
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="competencyModalLabel">Yetkinlik Atama</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                  <div class="mb-3">
                      <label>Parametre/Metot</label>
                      <select name="competency_id" class="form-select" required>
                          @foreach(\App\Models\Competency::all() as $c)
                              <option value="{{ $c->id }}">{{ $c->method }}</option>
                          @endforeach
                      </select>
                  </div>
                  <div class="mb-3">
                      <label>Yetki Tarihi</label>
                      <input type="date" name="granted_at" class="form-control" required>
                                    <div class="mb-3">
                      <label>Seviye</label>
                      <input type="text" name="level" class="form-control">
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
@endsection