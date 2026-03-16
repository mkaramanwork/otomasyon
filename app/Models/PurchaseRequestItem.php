<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequestItem extends Model
{
    protected $fillable = [
        'purchase_request_id','inventory_id','quantity','unit','description','supplier_id'
    ];

    public function purchaseRequest() {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function inventory() {
        return $this->belongsTo(Inventory::class)->with('technicalSpecification');

    }

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }
}
