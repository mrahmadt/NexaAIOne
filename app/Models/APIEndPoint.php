<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APIEndPoint extends Model
{
    use HasFactory;

        /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'api_end_points';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'apiName',
        'enableUsage',
        'requestSchema',
        'toolsConfig',
        'isActive',
        'ai_end_points_id',
    ];
/*
        'historyOptions',
        'toolsConfig',
        'requestSchema',
        'model',
*/

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'enableUsage' => 'boolean',
        'requestSchema' => 'array',
        'toolsConfig' => 'array',
        'isActive' => 'boolean',
    ];

    /**
     * Get the AIModels associated with this APIEndPoint.
     */
    public function aiendpoint()
    {
        return $this->belongsTo('App\Models\AIEndPoint', 'ai_end_points_id', 'id');
    }

    /**
     * Get the Tools associated with this APIEndPoint.
     */
    public function tools()
    {
        return $this->belongsToMany('App\Models\Tool', 'api_end_point_tool');
    }
    
    /**
     * Get the Apps associated with this API End Point.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function apps()
    {
        return $this->belongsToMany(App::class, 'app_api_end_point');
    }

}
