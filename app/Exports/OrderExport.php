<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderExport implements FromCollection, WithHeadings
{
    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function collection()
    {
        $rows = [];

        foreach ($this->order->items as $item) {
            $rows[] = [
                'Müşteri'       => $this->order->customer_name,
                'Email'         => $this->order->customer_email,
                'Tip'           => $this->order->type,
                'Durum'         => $this->order->status,
                'Ürün'          => $item->product->name,
                'Adet'          => $item->quantity,
                'Durum (Ürün)'  => $item->status,
                'Genel İskonto' => $this->order->global_discount ?? 0,
                'Toplam TL'     => $this->order->total_price,
                'Toplam USD'    => $this->order->total_price_usd,
            ];
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'Müşteri',
            'Email',
            'Tip',
            'Durum',
            'Ürün',
            'Adet',
            'Durum (Ürün)',
            'Genel İskonto',
            'Toplam TL',
            'Toplam USD',
        ];
    }
}