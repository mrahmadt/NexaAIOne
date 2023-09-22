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

    protected static function boot() {
        parent::boot();
        static::creating(function ($record) {
            // $collection = Collection::where(['collection_id', $record->collection_id])->first();
            $embedder = Embedder::where(['collection_id', $record->embedder_id])->first();
            $className = '\App\Services\\' . $embedder->className;
            $EmbedderClass = new $className($embedder->options);
            $EmbedderClass->create($record);
            //use App\Embedders\OpenAIEmbedding;
            if(!isset($record->embeds)){

                // $embeddingModel = new OpenAIEmbedding([
            }
        });
        // static::updating(function ($record) {
        //     $record->name = $record->name ?? uniqid();
        // });
    }    

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
