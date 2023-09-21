<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Api extends Model
{
    use HasFactory;

        /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'apis';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'endpoint',
        'enableUsage',
        'options',
        'isActive',
        'service_id',
        'collection_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'enableUsage' => 'boolean',
        'options' => 'array',
        'isActive' => 'boolean',
    ];

    /**
     * Get the AIModels associated with this API.
     */
    public function service()
    {
        return $this->belongsTo('App\Models\Service', 'service_id', 'id');
    }
    /**
     * Get the AIModels associated with this API.
     */
    public function collection()
    {
        return $this->belongsTo(Collection::class, 'collection_id', 'id');
    }

    /**
     * Get the Apps associated with this API.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function apps()
    {
        return $this->belongsToMany(App::class, 'api_app');
    }

}
