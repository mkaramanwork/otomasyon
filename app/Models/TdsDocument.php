<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TdsDocument extends Model
{
    protected $fillable = [
        'product_id',
        'brand_id',
        'product_group',
        'product_group_id',
        'product_code',
        'product_name',
        'description',
        'features',
        'parameters',
        'approvals',
    ];

    protected $casts = [
        'features' => 'array',
        'parameters' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productGroup()
    {
        return $this->belongsTo(ProductGroup::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}