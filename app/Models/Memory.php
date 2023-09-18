<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Memory extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'memories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'api_id',
        'sessionHash',
        'messages',
        'messagesMeta'
    ];

    protected $casts = [
        'input' => 'json',
        'output' => 'json',
        'backtrace' => 'json',
    ];

    public function api()
    {
        return $this->belongsTo(Api::class, 'api_id', 'id');
    }
}