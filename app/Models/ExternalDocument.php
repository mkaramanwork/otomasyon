<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalDocument extends Model
{
    protected $fillable = [
        'sequence_no','document_code','title','institution',
        'revision_no','publish_date','review_date','url',
        'file_path','created_by'
    ];

    public function revisions()
    {
        return $this->hasMany(ExternalDocumentRevision::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class,'created_by');
    }
}