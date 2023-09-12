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
        'isActive' => 'boolean',
        'supportHistory' => 'boolean',
        'supportCaching' => 'boolean',
    ];

    public function llms()
    {
        return $this->belongsToMany(LLM::class, 'ai_end_point_llm', 'ai_end_point_id', 'llm_id')->withTimestamps();
    }
}
