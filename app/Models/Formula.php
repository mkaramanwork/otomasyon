<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formula extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'product_id',
        'packaging_cost',
        'labor_cost',
        'shipping_cost',
        'tax',
        'profit_margin',
        'vat',
        'total_cost',   
        'added_by',
    ];

    // İlişkiler
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function details()
    {
        return $this->hasMany(FormulaDetail::class);
    }
    public function sdsDocuments()
    {
        return $this->hasMany(SdsDocument::class);
    }

}