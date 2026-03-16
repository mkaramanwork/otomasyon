<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Filling extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_order_id',
        'physical_formula_id',
        'filling_line_id',
        'filled_quantity',
        'volume',
        'density',
        'calculated_materials',
        'status',
        'notes',
    ];

    protected $casts = [
        'calculated_materials' => 'array',
    ];

    // 🔗 İlişki: İş emri
    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class);
    }

    // 🔗 İlişki: Fiziksel reçete
    public function physicalFormula()
    {
        return $this->belongsTo(PhysicalFormula::class, 'physical_formula_id');
    }

    // 🔗 İlişki: Dolum hattı
    public function fillingLine()
    {
        return $this->belongsTo(ProductionLine::class, 'filling_line_id');
    }

    // 🔧 Statü rozet rengi
    public function statusColor(): string
    {
        return match($this->status) {
            'pending' => 'secondary',
            'in_progress' => 'warning text-dark',
            'completed' => 'success',
            default => 'secondary',
        };
    }

    // 📦 Toplam dolum miktarı (kg)
    public function getTotalKgAttribute(): float
    {
        return round(
            ($this->filled_quantity ?? 0) *
            ($this->volume ?? 0) *
            ($this->density ?? 0),
            2
        );
    }

    
}