<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Pgvector\Laravel\Vector;
use DB;

class Chunk extends Model
{
    use HasFactory;

    protected $fillable = [
        'meta',
        'embeds',
        'collection_id',
        'document_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'meta' => 'array',
        'embeds' => Vector::class
    ];

    // Belongs to a Collection
    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    // Belongs to a Document
    public function document()
    {
        return $this->belongsTo(Document::class);
    }


}
