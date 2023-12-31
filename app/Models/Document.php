<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Pgvector\Laravel\Vector;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'content_tokens',
        'meta',
        'embeds',
        'collection_id',
        // 'disable_splitter',
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

    protected $hidden = [
        'embeds',
    ];

    protected static function embeddings($record) {
        $collection = Collection::where(['id'=>$record->collection_id])->first();
        if($collection->embedder_id){
            $embedder = Embedder::where(['id'=> $collection->embedder_id])->first();
            $className = '\App\Embedders\\' . $embedder->className;
            $EmbedderClass = new $className($embedder->options);
            $embeds = $EmbedderClass->execute($record->content);
            if($embeds && isset($embeds->embeddings[0]->embedding)){
                $record->embeds = $embeds->embeddings[0]->embedding;
                $record->content_tokens = $embeds->usage->totalTokens;
            }
        }
        return $record;
    }

    protected static function boot() {
        parent::boot();
        static::creating(function ($record) {
            if(!isset($record->embeds) && isset($record->content)){
                $record = self::embeddings($record);
            }
        });
        static::updating(function ($record) {
            if(!isset($record->embeds) && $record->content != $record->original['content']){
                $record = self::embeddings($record);
            }
        });
    }

    // Belongs to a Collection
    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

}
