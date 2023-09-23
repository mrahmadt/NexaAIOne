<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'authToken',
        'defaultTotalReturnDocuments',
        'loader_id',
        'splitter_id',
        'embedder_id',
    ];
    
    protected static function newAuthToken() {
        return bin2hex(openssl_random_pseudo_bytes(16)). bin2hex(random_bytes(16));
    }

    /**
     * Get the loader associated with the collection.
     */
    public function loader()
    {
        return $this->belongsTo(Loader::class);
    }

    /**
     * Get the spliter associated with the collection.
     */
    public function splitter()
    {
        return $this->belongsTo(Splitter::class);
    }

    /**
     * Get the embedder associated with the collection.
     */
    public function embedder()
    {
        return $this->belongsTo(Embedder::class);
    }

    /**
     * Get the document associated with the collection.
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

}
