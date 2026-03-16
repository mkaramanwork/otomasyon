<?php

namespace App\Imports;

use App\Models\SalesProduct;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class SalesProductsImport implements ToCollection, WithHeadingRow
{// ürünleri içeri atan  donksiyon 
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            SalesProduct::updateOrCreate(
                ['id' => $row['id']], // eşleşme kriteri
                [
                    'name'              => $row['name'],
                    'performance_class' => $row['performance_class'],
                    'volume'            => $row['volume'],
                    'price_usd'         => $row['price_usd'],
                    'price_try'         => $row['price_try'],
                ]
            );
        }
    }


}