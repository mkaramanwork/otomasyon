<?php

class Tank extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'capacity_kg', 'added_by'];

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}