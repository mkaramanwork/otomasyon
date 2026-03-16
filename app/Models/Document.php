<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'doc_no','title','revision_no','publish_date',
        'last_revision_date','review_date','revision_reason',
        'created_by','status'
    ];

    public function revisions()
    {
        return $this->hasMany(DocumentRevision::class);
    }
    public function latestRevision()
    {
        return $this->hasOne(DocumentRevision::class)->latestOfMany();
    }
}