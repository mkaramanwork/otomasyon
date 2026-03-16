<p>Merhaba,</p>
<p>Yeni doküman eklendi:</p>
<ul>
    <li><strong>Doküman No:</strong> {{ $document->doc_no }}</li>
    <li><strong>Başlık:</strong> {{ $document->title }}</li>
    <li><strong>Revizyon No:</strong> R{{ $document->revision_no }}</li>
    <li><strong>Yayın Tarihi:</strong> {{ $document->publish_date }}</li>
    <li><strong>Durum:</strong> {{ ucfirst($document->status) }}</li>
</ul>
<p>Ekleyen: {{ $document->created_by }}</p>
<p>Murat KARAMAN ERP</p>