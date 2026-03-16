<?php

class LabApproval extends Model
{
    use HasFactory;

    protected $fillable = ['recipe_id', 'approved_by', 'approved_at', 'notes'];

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}