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
        'content',
        'content_tokens',
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
            if(!isset($record->embeds)){
                $embedder = Embedder::where(['collection_id', $record->embedder_id])->first();
                $className = '\App\Embedders\\' . $embedder->className;
                $EmbedderClass = new $className($embedder->options);
                $embeds = $EmbedderClass->create($record);
                if($embeds && isset($embeds->embeddings[0]->embedding)){
                    $record->embeds = $embeds->embeddings[0]->embedding;
                    $record->tokens = $embeds->usage->totalTokens;
                }
            }
        });
        static::updating(function ($record) {
            if(!isset($record->embeds) ) {
                $embedder = Embedder::where(['collection_id', $record->embedder_id])->first();
                $className = '\App\Embedders\\' . $embedder->className;
                $EmbedderClass = new $className($embedder->options);
                $embeds = $EmbedderClass->create($record);
                if($embeds && isset($embeds->embeddings[0]->embedding)) {
                    $record->embeds = $embeds->embeddings[0]->embedding;
                    $record->tokens = $embeds->usage->totalTokens;
                }
            }
        });
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
