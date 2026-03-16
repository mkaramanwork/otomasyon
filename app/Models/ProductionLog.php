<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_order_id',
        'event',
        'description',
        'logged_at',
        'user_id',
    ];

    public function order()
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}