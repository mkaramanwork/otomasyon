<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRelation extends Model
{
    protected $fillable = [
        'product_id','relation_type','relation_id','status','supplier_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}

