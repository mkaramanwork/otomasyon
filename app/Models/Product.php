<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'unit', 'added_by'];

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function orders()
    {
        return $this->hasMany(ProductionOrder::class);
    }

    public function formulas()
    {
        return $this->hasMany(Formula::class);
    }

    public function relations()
    {
        return $this->hasMany(ProductRelation::class);
    }

    public function labels()
    {
        return $this->hasMany(Label::class);
    }

    public function barcodes()
    {
        return $this->hasMany(Barcode::class);
    }

    public function packageBoxes()
    {
        return $this->hasMany(PackageBox::class);
    }

    public function caps()
    {
        return $this->hasMany(Cap::class);
    }
    public function physicalFormulas()
    {
        return $this->hasMany(PhysicalFormula::class);
    }
    public function bottles()
    {
        return $this->hasMany(\App\Models\Bottle::class);
    }
    public function tdsDocuments()
    {
        return $this->hasMany(\App\Models\TdsDocument::class);
    }

    public function certificates()
    {
        return $this->hasMany(\App\Models\Certificate::class);
    }



}