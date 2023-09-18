<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debug extends Model
{
    use HasFactory;
    protected $table = 'debugs';
    
    protected $fillable = [
        'api_id',
        'session',
        'input',
        'output',
        'backtrace',
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