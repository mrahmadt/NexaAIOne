<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LLM extends Model
{
    use HasFactory;

        /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'llms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'description', 
        'modelName', 
        'ownedBy', 
        'maxTokens'
    ];

    /**
     * Get the AIEndPoints associated with this LLM.
     */
    public function aiEndPoints()
    {
        return $this->belongsToMany('App\Models\AIEndPoint', 'ai_end_point_llm');
    }

    /**
     * Get the APIEndPoints associated with this LLM.
     */
    public function apiEndPoints()
    {
        return $this->belongsToMany('App\Models\APIEndPoint', 'llm_api_end_point');
    }

}
