<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ProductionOrder;




class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'material_id', 'material_type',
        'amount', 'related_recipe_id', 'triggered_by'
    ];

public function material()
{
    return $this->morphTo();
}

public function triggeredBy()
{
    return $this->belongsTo(User::class, 'triggered_by');
}

public function productionOrder()
{
    return $this->belongsTo(ProductionOrder::class, 'related_recipe_id');
}

}