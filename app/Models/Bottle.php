<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bottle extends Model
{
    protected $fillable = ['product_id','name','description','file_path'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}