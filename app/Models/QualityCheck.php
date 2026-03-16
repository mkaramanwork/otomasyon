<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QualityCheck extends Model
{
    protected $fillable = [
        'raw_material_id', 'batch_number', 'delivery_note_number',
        'received_at', 'supplier_name', 'kategori', 'amount',
        'unit', 'notes', 'status', 'checked_by'
    ];

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }
}
