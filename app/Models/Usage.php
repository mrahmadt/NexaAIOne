<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usage extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_id',
        'api_id',
        'hits',
        'promptTokens',
        'completionTokens',
        'totalTokens',
        'date',
    ];

    public function app()
    {
        return $this->belongsTo(App::class);
    }

    public function api()
    {
        return $this->belongsTo(Api::class);
    }
}