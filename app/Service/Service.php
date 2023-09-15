<?php

namespace App\AIEndPoints;

use Illuminate\Database\Eloquent\Model;

abstract class Service{
    // abstract public static function getSchema();
    protected static $REQUEST_SCHEMA = ['A'];

    public static function getSchema(?int $AIEndPointID = null, ?Model $AIEndPoint = null){
        $requestSchema = static::$REQUEST_SCHEMA;
        $llmOptions = [];
        $llmMaxTokens = [];
        if ($AIEndPointID !== null) {
            $AIEndPoint = $AIEndPoint::where(['id'=>$AIEndPointID, 'isActive'=>true ])->first();
            $llms = $AIEndPoint->llms()->get();
        }elseif ($AIEndPoint !== null) {
            $llms = $AIEndPoint->llms()->get();
        }
        foreach ($llms as $llm) {
            $llmOptions[$llm->modelName] = $llm->name;
            $llmMaxTokens[$llm->modelName] = $llm->maxTokens;
        }
        foreach ($requestSchema as $index => $subArray) {
            if(isset($requestSchema[$index]['name']) && $requestSchema[$index]['name'] == 'model') {
                $requestSchema[$index]['options'] = $llmOptions;
                $requestSchema[$index]['maxTokens'] = $llmMaxTokens;
                $requestSchema[$index]['default'] = implode(',',array_keys($llmOptions));
            }

            $requestSchema[$index] = array_merge(
                [
                    '_mandatory' => true,
                    '_allowApiOption' => true,
                    '_noDefaultValue' => false,
                    'desc' => null,
                    'type' => 'string',
                    'default' => null,
                    'required' => false,
                    'isApiOption' => true,
                ],
                $requestSchema[$index],
            );

        }
        return $requestSchema;
    }
}

