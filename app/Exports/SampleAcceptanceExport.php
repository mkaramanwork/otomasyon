<?php
namespace App\Exports;

use App\Models\SampleAcceptance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class SampleAcceptanceExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $samples = SampleAcceptance::with(['labProduct','parameters'])->get();

        $rows = [];
        foreach ($samples as $sample) {
            $params = $sample->parameters->map(fn($p) => $p->method_tr)->implode(', ');

            $rows[] = [
                $sample->report_no ?? '-',                                        // 1. Rapor No
                $sample->batch_no ?? '-',                                        // 2. Batch No
                $sample->taken_at ? Carbon::parse($sample->taken_at)->format('d.m.Y H:i') : '-', // 3. Numune Alma Tarihi & Saati
                $sample->company_name ?? '-',                                    // 4. Firma
                $sample->labProduct->product_name ?? '-',                        // 5. Ürün
                $params ?: '-',                                                  // 6. Parametreler
            ];
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'Rapor No',
            'Batch No',
            'Numune Alma Tarihi & Saati',
            'Firma',
            'Ürün Adı',
            'Parametreler (Metot TR)',
        ];
    }
}
