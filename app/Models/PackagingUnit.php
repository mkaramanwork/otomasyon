<?php

class PackagingUnit extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'added_by'];

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}