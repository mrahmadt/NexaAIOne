<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'meta',
        'collection_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'meta' => 'array',
    ];

    protected static function boot() {
        parent::boot();
    
        static::creating(function ($record) {
            $record->name = $record->name ?? uniqid();
        });
    
        static::updating(function ($record) {
            $record->name = $record->name ?? uniqid();
        });
    
    }    

    // Belongs to a Collection
    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }
    
    /**
    * Get the chunk associated with the Document.
    */
    public function chunks()
    {
        return $this->hasMany('App\Models\Chunk');
    }
}
