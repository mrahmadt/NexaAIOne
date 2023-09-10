<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class App extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

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
        'email',
        'isActive'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'isActive' => 'boolean',
    ];

    /**
     * Get the API End Points associated with this App.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function apiEndPoints()
    {
        return $this->belongsToMany(APIEndPoint::class, 'app_api_end_point');
    }

}
