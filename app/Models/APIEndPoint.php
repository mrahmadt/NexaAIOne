<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APIEndPoint extends Model
{
    use HasFactory;

        /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'api_end_points';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'apiName',
        'enableUsage',
        'enableHistory',
        'historyMethod',
        'historyOptions',
        'requestSchema',
        'toolsConfig',
        'enableCaching',
        'cachingObjects',
        'cachingPeriod',
        'isActive',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'enableUsage' => 'boolean',
        'requestSchema' => 'array',
        'historyOptions' => 'array',
        'toolsConfig' => 'array',
        'enableCaching' => 'boolean',
        'isActive' => 'boolean',

        // 'cachingObjects' might need a custom cast if you plan to work with it as an array
    ];

    /**
    * History Method
    *
    * @var array
    */
    public const HISTORYMETHOD = [
        'Truncate','Summary','Embeddings'
    ];

    /**
    * get History Method
    */
    public function getHistoryMethod()
    {
        return self::HISTORYMETHOD[ $this->attributes['historyMethod_id'] ];
    }
    
    /**
   * returns the id of a given History Method
   *
   * @param string $historyMethod  tool's History Method
   * @return int HistoryMethodID
   */
    public static function getHistoryMethodID($historyMethod)
    {
        return array_search($historyMethod, self::HISTORYMETHOD);
    }

    /**
    * set History Method
    */
    public function setHistoryMethodAttribute($value)
    {
        $historyMethod = self::getHistoryMethodID($value);
        if ($historyMethod) {
            $this->attributes['historyMethod_id'] = $historyMethod;
        }
    }

    /**
     * Get the AIModels associated with this APIEndPoint.
     */
    public function LLMs()
    {
        return $this->belongsToMany('App\Models\LLM', 'api_end_point_llm');
    }

    /**
     * Get the Tools associated with this APIEndPoint.
     */
    public function tools()
    {
        return $this->belongsToMany('App\Models\Tool', 'api_end_point_tool');
    }
    
    /**
     * Get the Apps associated with this API End Point.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function apps()
    {
        return $this->belongsToMany(App::class, 'app_api_end_point');
    }

}
