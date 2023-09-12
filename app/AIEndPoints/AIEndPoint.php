<?php

namespace App\AIEndPoints;

use Illuminate\Database\Eloquent\Model;

abstract class AIEndPoint{
    // abstract public static function getSchema();
    protected static $REQUEST_SCHEMA = ['A'];

    public static function getSchema(?int $AIEndPointID = null, ?Model $AIEndPoint = null){
        $requestSchema = static::$REQUEST_SCHEMA;
        $llmOptions = [];
        if ($AIEndPointID !== null) {
            $AIEndPoint = $AIEndPoint::where(['id'=>$AIEndPointID, 'isActive'=>true ])->first();
            $llms = $AIEndPoint->llms()->get();
        }elseif ($AIEndPoint !== null) {
            $llms = $AIEndPoint->llms()->get();
        }
        foreach ($llms as $llm) {
            $llmOptions[$llm->modelName] = $llm->name;
            
        }
        foreach ($requestSchema as $index => $subArray) {
            if(isset($requestSchema[$index]['name']) && $requestSchema[$index]['name'] == 'model') {
                $requestSchema[$index]['options'] = $llmOptions;
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

            // "options": {
            //     "gpt-4": "GPT-4",
            //     "gpt-4-32k": "GPT-4 32k",
            //     "whisper-1": "Whisper"
            // },
        }
        return $requestSchema;
    }
}

