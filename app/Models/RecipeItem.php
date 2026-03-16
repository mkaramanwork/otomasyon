<?php

class RecipeItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipe_id', 'material_type', 'material_id',
        'percentage', 'calculated_kg', 'added_by'
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}