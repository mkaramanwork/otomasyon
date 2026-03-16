<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
    'name',
    'contact_person',
    'email',
    'phone',
    'address',
    'added_by'
];

public function user()
{
    return $this->belongsTo(User::class, 'added_by');
}

}
