@extends('layouts.admin')
@section('title','Sınav Çöz')
@section('content')
<div class="container">

    <!-- ✅ Sınav Bilgileri -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">📘 {{ $exam->title }} Çözüm</div>
        <div class="card-body">
            <p><strong>Kategori:</strong> {{ $exam->category->name }}</p>
            <p><strong>Soru Sayısı:</strong> {{ $exam->questions->count() }}</p>
        </div>
    </div>

    <!-- ✅ Sınav Soruları -->
    <div class="card">
        <div class="card-header bg-primary text-white">📝 Sorular</div>
        <div class="card-body">
            <form action="{{ route('exams.submit',$exam->id) }}" method="POST">
                @csrf

                @foreach($exam->questions as $index => $q)
                <div class="mb-4">
                    <h5>{{ $index+1 }}. {{ $q->question_text }}</h5>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answers[{{ $q->id }}]" value="A" required>
                        <label class="form-check-label">A) {{ $q->option_a }}</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answers[{{ $q->id }}]" value="B">
                        <label class="form-check-label">B) {{ $q->option_b }}</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answers[{{ $q->id }}]" value="C">
                        <label class="form-check-label">C) {{ $q->option_c }}</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answers[{{ $q->id }}]" value="D">
                        <label class="form-check-label">D) {{ $q->option_d }}</label>
                    </div>
                </div>
                @endforeach

                <button type="submit" class="btn btn-success">Sınavı Bitir</button>
            </form>
        </div>
    </div>

</div>
@endsection