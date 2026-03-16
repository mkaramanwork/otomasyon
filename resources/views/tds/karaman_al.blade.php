@extends('layouts.admin')

@section('title','Karaman_Al Asistanı')

@section('content')
<div class="container">
    <h4 class="mb-3">🤖 Karaman_Al Asistanı</h4>

    <!-- Soru Formu -->
    <form action="{{ route('tds.ask') }}" method="POST" class="mb-4">
        @csrf
        <div class="mb-3">
            <label for="question" class="form-label">Sorunuz</label>
            <textarea name="question" id="question" class="form-control" rows="5" placeholder="TDS ile ilgili sorunuzu yazın..." required></textarea>
        </div>
        <button type="submit" class="btn btn-danger">Sor</button>
    </form>


    <!-- Cevap Alanı -->
    @isset($answer)
    <div class="alert alert-info">
        <strong>Karaman_Al:</strong> {{ $answer }}
    </div>
    @endisset

    <!-- Geçmiş Konuşmalar Accordion -->
    <div class="accordion" id="conversationAccordion">
        @forelse($prompts as $index => $prompt)
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading{{ $index }}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                        data-bs-target="#collapse{{ $index }}" aria-expanded="false" aria-controls="collapse{{ $index }}">
                    📝 Soru: {{ $prompt->question }}
                </button>
            </h2>
            <div id="collapse{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $index }}" data-bs-parent="#conversationAccordion">
                <div class="accordion-body">
                    <strong>Cevap:</strong> {{ $prompt->answer }}
                    <br><small class="text-muted">Tarih: {{ $prompt->created_at->format('d.m.Y H:i') }}</small>
                </div>
            </div>
        </div>
        @empty
        <p class="text-muted">Henüz hiç soru sorulmadı.</p>
        @endforelse
    </div>
</div>
@endsection