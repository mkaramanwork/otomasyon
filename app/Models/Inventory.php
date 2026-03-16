<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_code',
        'product_name',
        'type',
        'type_other',
        'description',
    ];

    public function technicalSpecification()
    {
        return $this->hasOne(TechnicalSpecification::class);
    }
}