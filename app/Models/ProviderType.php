<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function documentTypes()
    {
        return $this->belongsToMany(DocumentType::class, 'document_type_provider_type');
    }
}
