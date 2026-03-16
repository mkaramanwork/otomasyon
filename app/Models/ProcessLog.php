<?php

class ProcessLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'action', 'details'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loggable()
    {
        return $this->morphTo();
    }
}