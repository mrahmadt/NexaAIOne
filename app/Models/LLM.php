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

    public function aiEndPoints()
    {
        return $this->belongsToMany(AIEndPoint::class, 'ai_end_point_llm', 'llm_id', 'ai_end_point_id')->withTimestamps();
    }
}
