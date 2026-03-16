<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    protected $fillable = [
        'request_code','status','ordered_at','accepted_at',
        'lab_manager_name','general_manager_name','procurement_name',
        'order_name','control_name','acceptance_name'
    ];

    public function items() {
        return $this->hasMany(PurchaseRequestItem::class);
    }
}