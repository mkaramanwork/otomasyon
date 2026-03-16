<?php

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'total_kg', 'batch_number',
        'production_datetime', 'status', 'created_by'
        
    ];

    protected $dates = ['production_datetime'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(RecipeItem::class);
    }
    public function formula()
    {
        return $this->belongsTo(Formula::class);
    }

    
}