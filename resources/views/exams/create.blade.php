@extends('layouts.admin')
@section('title','Yeni Sınav')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">📝 Yeni Sınav</div>
        <div class="card-body">
            <form action="{{ route('exams.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label>Başlık</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Kategori</label>
                    <select id="categorySelect" name="category_id" class="form-select" required>
                        <option value="">-- Seçiniz --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Soru Sayısı (otomatik)</label>
                    <input type="number" id="questionCount" name="question_count" class="form-control" readonly>
                </div>

                <!-- ✅ Soru Seçim Alanı -->
                <div class="mb-3">
                    <label>Sorular</label>
                    <div class="table-responsive" style="max-height:300px; overflow-y:auto;">
                        <table class="table table-sm table-bordered" id="questionsTable">
                            <thead>
                                <tr>
                                    <th>Seç</th>
                                    <th>Soru</th>
                                    <th>Kategori</th>
                                    <th>Zorluk</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($questions as $q)
                                <tr data-category="{{ $q->category_id }}">
                                    <td><input type="checkbox" name="question_ids[]" value="{{ $q->id }}" class="question-checkbox"></td>
                                    <td>{{ $q->question_text }}</td>
                                    <td>{{ $q->category->name }}</td>
                                    <td>{{ $q->difficulty }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <input type="hidden" name="created_by" value="{{ auth()->id() }}">
                <button type="submit" class="btn btn-success">Kaydet</button>
            </form>
        </div>
    </div>
</div>

<!-- ✅ JS Mantığı -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.question-checkbox');
    const questionCountInput = document.getElementById('questionCount');
    const categorySelect = document.getElementById('categorySelect');
    const rows = document.querySelectorAll('#questionsTable tbody tr');

    // Seçilen checkbox sayısını otomatik güncelle
    checkboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            const selected = document.querySelectorAll('.question-checkbox:checked').length;
            questionCountInput.value = selected;
        });
    });

    // Kategoriye göre filtreleme
    categorySelect.addEventListener('change', () => {
        const selectedCat = categorySelect.value;
        rows.forEach(row => {
            if (!selectedCat || row.dataset.category === selectedCat) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>
@endsection