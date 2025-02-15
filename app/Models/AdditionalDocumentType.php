<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdditionalDocumentType extends Model
{
    use HasFactory;

    protected $fillable = ['type_name'];

    public function additionalDocuments(): HasMany
    {
        return $this->hasMany(AdditionalDocument::class, 'document_type_id');
    }
}
