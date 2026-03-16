<?php

class LineAssignment extends Model
{
    use HasFactory;

    protected $fillable = ['recipe_id', 'line_id', 'status', 'assigned_at', 'assigned_by'];

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}