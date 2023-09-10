<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usage extends Model
{
    use HasFactory;

     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'api_debugs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'api_end_point_id',
        'type',
        'output',
        'executionTime'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'output' => 'array',
        'executionTime' => 'float'
    ];

    /**
     * Get the APIEndPoint related to this APIDebug.
     */
    public function apiEndPoint()
    {
        return $this->belongsTo('App\Models\APIEndPoint', 'api_end_point_id');
    }

    /**
     * Convert the output to an array format.
     *
     * @return array
     */
    public function getOutputAsArray()
    {
        return json_decode($this->output, true);
    }

    /**
     * A method to retrieve the type of this debug entry.
     *
     * @return string
     */
    public function getDebugType()
    {
        return $this->type;
    }

}
