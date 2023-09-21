<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    use HasFactory;

        /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'apps';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'description',
        'owner',
        'authToken'
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'isActive' => 'boolean',
    ];

    protected static function newAuthToken() {
        return bin2hex(openssl_random_pseudo_bytes(16)). bin2hex(random_bytes(16));
    }


    /**
    * Get the APIs associated with this App.
    */
    public function apis()
    {
        return $this->belongsToMany(Api::class, 'api_app');
    }

}
