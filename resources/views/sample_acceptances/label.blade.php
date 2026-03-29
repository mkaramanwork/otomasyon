<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Numune Etiketi</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100mm;   /* Etiket genişliği */
            height: 50mm;   /* Etiket yüksekliği */
            background: none;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
        }

        .etiket {
            border: 1px solid #000;
            padding: 5px;
            width: 100mm;
            height: 50mm;
            box-sizing: border-box;
        }

        h4 {
            margin: 0 0 5px 0;
            text-align: center;
            font-size: 12px;
        }

        p {
            margin: 2px 0;
        }

        @media print {
            @page {
                size: 100mm 50mm; /* ✅ sadece tek etiket boyutu */
                margin: 0;
            }
            html, body {
                width: 100mm;
                height: 50mm;
            }
            .etiket {
                page-break-inside: avoid;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="etiket">
        <h4>Numune Etiketi</h4>
        <p><strong>Firma:</strong> {{ $sample->company_name ?? '-' }}</p>
        <p><strong>Batch No:</strong> {{ $sample->batch_no ?? '-' }}</p>
        <p><strong>Ülke:</strong> {{ $sample->country ?? '-' }}</p>
        <p><strong>Kabul Tarihi:</strong> {{ $sample->taken_at?->format('d.m.Y H:i') ?? '-' }}</p>
        <p><strong>Kabul Eden:</strong> {{ $sample->acceptedUser->name ?? '-' }}</p>
        <p>Dokuman Kodu Alanı</p>
    </div>
</body>
</html> 