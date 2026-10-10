<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SdsDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'formula_id',
        'brand_id',
        'document_no',
        'product_name_snapshot',
        'product_code_snapshot',
        'brand_name_snapshot',
        'formula_name_snapshot',
        'company_snapshot',
        'formula_snapshot',
        'regulation_profile',
        'jurisdiction',
        'language',
        'version',
        'revision_no',
        'issue_date',
        'revision_date',
        'regulatory_snapshot',
        'classification_snapshot',
        'label_snapshot',
        'status',
        'calculation_completed',
        'regulatory_check_completed',
        'calculated_at',
        'regulatory_checked_at',
        'created_by',
        'approved_by',
        'approved_at',
        'word_file',
        'pdf_file',
        'notes',
    ];

    protected $casts = [
        'company_snapshot' => 'array',
        'formula_snapshot' => 'array',
        'regulatory_snapshot' => 'array',
        'classification_snapshot' => 'array',
        'label_snapshot' => 'array',

        'issue_date' => 'date',
        'revision_date' => 'date',

        'calculation_completed' => 'boolean',
        'regulatory_check_completed' => 'boolean',

        'calculated_at' => 'datetime',
        'regulatory_checked_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function formula()
    {
        return $this->belongsTo(Formula::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function sections()
    {
        return $this->hasMany(SdsSection::class);
    }

    public function revisions()
    {
        return $this->hasMany(SdsRevision::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}