<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIEndPoint extends Model
{
    use HasFactory;

        /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ai_end_points';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'description',
        'className',
        'ApiReference',
        'requestSchema', 
        'supportHistory', 
        'supportCaching', 
        'isActive', 
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'requestSchema' => 'array',
        'isActive' => 'boolean',
        'supportHistory' => 'boolean',
        'supportCaching' => 'boolean',
    ];
    /**
     * Get the AIModels associated with this AIEndPoint.
     */
    public function LLMs()
    {
        return $this->belongsToMany('App\Models\LLM', 'ai_end_point_llm');
    }

}
