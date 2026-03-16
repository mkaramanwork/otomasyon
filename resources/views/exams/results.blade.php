@extends('layouts.admin')
@section('title','Sınav Sonuçları')
@section('content')
<div class="container">

    <!-- ✅ Sınav Bilgileri -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">📘 {{ $exam->title }} Sonuçları</div>
        <div class="card-body">
            <p><strong>Kategori:</strong> {{ $exam->category->name }}</p>
            <p><strong>Soru Sayısı:</strong> {{ $exam->questions->count() }}</p>
        </div>
    </div>

    <!-- ✅ Kullanıcı Sonuçları -->
    <div class="card">
        <div class="card-header bg-primary text-white">📝 Cevaplarım</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Soru</th>
                        <th>Seçilen Cevap</th>
                        <th>Doğru Cevap</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignment->answers as $index => $answer)
                    <tr>
                        <td>{{ $index+1 }}</td>
                        <td>{{ $answer->question->question_text }}</td>
                        <td>{{ $answer->selected_option }}</td>
                        <td>{{ $answer->question->correct_option }}</td>
                        <td>
                            @if($answer->is_correct)
                                <span class="badge bg-success">Doğru</span>
                            @else
                                <span class="badge bg-danger">Yanlış</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- ✅ Puan Hesabı -->
            @php
                $total = $assignment->answers->count();
                $correct = $assignment->answers->where('is_correct',true)->count();
                $score = $total > 0 ? round(($correct/$total)*100) : 0;
            @endphp

            <div class="alert alert-info mt-3">
                Toplam: {{ $total }} soru<br>
                Doğru: {{ $correct }}<br>
                Puan: <strong>{{ $score }}</strong>
            </div>
        </div>
    </div>

</div>
@endsection