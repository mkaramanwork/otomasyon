<p>Merhaba,</p>
<p>{{ $document->title }} dokümanı için yeni revizyon eklendi:</p>
<ul>
    <li><strong>Doküman No:</strong> {{ $document->doc_no }}</li>
    <li><strong>Revizyon No:</strong> R{{ $revision->revision_no }}</li>
    <li><strong>Revizyon Tarihi:</strong> {{ $revision->revision_date }}</li>
    <li><strong>Gözden Geçirme Tarihi:</strong> {{ $revision->review_date }}</li>
    <li><strong>Nedeni:</strong> {{ $revision->change_note }}</li>
</ul>
<p>Dosya: <a href="{{ asset('storage/'.$revision->file_path) }}">Görüntüle</a></p>
<p>Güncelleyen: {{ $revision->updated_by }}</p>
<p>Murat KARAMAN ERP</p>