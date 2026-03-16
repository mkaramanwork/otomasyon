<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalesProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'performance_class',
        'volume',
        'price_usd',
        'price_try',
    ];
}
