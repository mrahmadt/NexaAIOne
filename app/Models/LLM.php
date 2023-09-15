<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Llm extends Model
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

    public function services()
    {
        return $this->belongsToMany(Service::class, 'llm_service', 'llm_id', 'service_id')->withTimestamps();
    }
}
