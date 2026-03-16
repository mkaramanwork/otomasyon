<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductionOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'production_line_id', 'formula_id', 'customer_id',
        'planned_at', 'quantity_kg', 'notes', 'batch_number', 'unit',
        'created_by', 'status',
    ];

    protected $casts = [
        'planned_at' => 'datetime',
    ];

    // İlişkiler
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function line()
    {
        return $this->belongsTo(ProductionLine::class, 'production_line_id');
    }

    public function formula()
    {
        return $this->belongsTo(Formula::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function steps()
    {
        return $this->hasMany(ProductionStep::class);
    }

    public function productionFormulaDetails()
    {
        return $this->hasMany(ProductionFormulaDetail::class);
    }

    public function logs()
    {
        return $this->hasMany(ProductionLog::class);
    }

    public function filling()
    {
        return $this->hasOne(Filling::class);
    }
    public function fillingRecipe()
    {
        return $this->belongsTo(Formula::class, 'filling_recipe_id');
    }
    public function productionLine()
    {
        return $this->belongsTo(ProductionLine::class, 'production_line_id');
    }



}