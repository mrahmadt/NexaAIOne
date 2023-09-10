<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tools';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'isActive',
        'aiName',
        'aiDescription',
        'aiParameters',
        'params',
        'type_id',
        'className',
        'apiURL',
        'apiMethod',
        'apiHeader',
        'apiBodyMethod',
        'apiBody',
        'exposeAsAPI'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'isActive' => 'boolean',
        'aiParameters' => 'array',
        'params' => 'array',
        'apiHeader' => 'array',
        'apiBody' => 'array',
        'exposeAsAPI' => 'boolean',
    ];

    /**
    * Tools' types
    *
    * @var array
    */
    public const TYPE = [
      'class',
      'api'
    ];

    /**
    * Tools' API Method
    *
    * @var array
    */
    public const APIMETHOD = [
        'post',
        'get',
        'delete',
        'put',
    ];

    /**
    * Tools' API Body Method
    *
    * @var array
    */
    public const APIBODYMETHOD = [
        'none','form-data' ,'x-www-form-urlencoded' ,'raw' ,'binary'
    ];

    /**
    * get tool API Body Method
    */
    public function getAPIBodyMethod()
    {
        return self::APIBODYMETHOD[ $this->attributes['apibodymethod_id'] ];
    }
    
    /**
   * returns the id of a given API Body Method
   *
   * @param string $apiBodyMethod  tool's API Body Method
   * @return int APIMethodID
   */
    public static function getAPIBodyMethodID($apiBodyMethod)
    {
        return array_search($apiBodyMethod, self::APIBODYMETHOD);
    }

    /**
    * set tool API Body Method
    */
    public function setAPIBodyMethodAttribute($value)
    {
        $apiBodyMethod = self::getAPIBodyMethodID($value);
        if ($apiBodyMethod) {
            $this->attributes['apiBodyMethod_id'] = $apiBodyMethod;
        }
    }

    /**
    * get tool API Method
    */
    public function getAPIMethod()
    {
        return self::APIMETHOD[ $this->attributes['apiMethod_id'] ];
    }
    
    /**
   * returns the id of a given API Method
   *
   * @param string $apiMethod  tool's API Method
   * @return int APIMethodID
   */
    public static function getAPIMethodID($apiMethod)
    {
        return array_search($apiMethod, self::APIMETHOD);
    }

    /**
    * set tool API Method
    */
    public function setAPIMethodAttribute($value)
    {
        $apiMethod = self::getAPIMethodID($value);
        if ($apiMethod) {
            $this->attributes['apiMethod_id'] = $apiMethod;
        }
    }



    /**
    * get tool type
    */
    public function getType()
    {
        return self::TYPE[ $this->attributes['type_id'] ];
    }
    
    /**
   * returns the id of a given type
   *
   * @param string $type  tool's type
   * @return int typeID
   */
    public static function getTypeID($type)
    {
        return array_search($type, self::TYPE);
    }

    /**
    * set tool type
    */
    public function setTypeAttribute($value)
    {
        $typeID = self::getTypeID($value);
        if ($typeID) {
            $this->attributes['type_id'] = $typeID;
        }
    }
 
    /**
     * Get the APIEndPoints associated with this Tool.
     */
    public function apiEndPoints()
    {
        return $this->belongsToMany('App\Models\APIEndPoint', 'api_end_point_tool');
    }

    /**
     * Determine if the tool is of type 'class'.
     *
     * @return bool
     */
    public function isClassType()
    {
        return $this->toolType === 'class';
    }

    /**
     * Determine if the tool is of type 'api'.
     *
     * @return bool
     */
    public function isApiType()
    {
        return $this->toolType === 'api';
    }

    /**
     * Helper method to retrieve the headers for API tools.
     *
     * @return array|null
     */
    public function getApiHeaders()
    {
        return $this->isApiType() ? $this->apiHeader : null;
    }

    /**
     * Any additional methods or helper functions for the Tool can be added here.
     * For instance: query scopes, CRUD operations, data transformations, etc.
     */
}
