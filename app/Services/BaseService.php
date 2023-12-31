<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use App\Features\HasDebug;
use App\Models\Service as ServiceModel;

abstract class BaseService{
    use HasDebug;

    protected $usage = [
        'promptTokens'=>0,
        'completionTokens'=>0,
        'totalTokens'=>0,
    ];
    /**
     * An array of extra responses for the service.
     *
     * @var array
     */
    protected $extraResponses = [];

    /**
     * An array of options for the service.
     *
     * @var array
     */
    protected $options = [];

    /**
     * The API model for the service.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $ApiModel;

    /**
     * An array of feature properties for the service.
     *
     * @var array
    */
    protected $features = [];

    /**
     * Returns a response message for the service.
     *
     * @param array $response
     * @return array
     */
    protected function responseMessage($response = []){
        $defaultResponse = [
            'status' => false,
            'message' => null,
        ];
        if(isset($this->options['debug']) && $this->options['debug']) {
            $this->debug('output', $response);
            $debug = $this->saveDebug();
            $defaultResponse['debug'] = $debug;
        }
        $response = array_merge($defaultResponse, $this->extraResponses , $response);
        return $response;
    }

    /**
     * Initializes the options for the service.
     *
     * This will be used when we start an API, it will be called from the API controller to initialize the service's options
     * 
     * @param array $userOptions
     * @return void
     */
    protected function initOptions($userOptions){
        $defaultOptions = [];
        foreach($this->features as $featureProperty)
        {
            if(property_exists($this, $featureProperty)){
                $defaultOptions = array_merge($defaultOptions, static::${$featureProperty});
            }
        }

        $apiOptions=[];
        $NotOptions=[];
        $sysOptions=[];
        foreach($this->ApiModel->options as $group => $options){
            foreach ($options as $index => $item) {
                if(isset($item['isApiOption']) && $item['isApiOption']){
                    $apiOptions[$item['name']] = $item['default'] ?? $defaultOptions[$item['name']]['default'] ?? null;
                }elseif(( !isset($item['isApiOption']) || $item['isApiOption'] == false )){
                    $NotOptions[$item['name']] = $item['default'] ?? $defaultOptions[$item['name']]['default'] ?? null;
                }
                $sysOptions[$item['name']] = $item;
            }
        }

        //TODO: userOptions check "false" & "true" values, convert them to boolean
        $this->options = array_merge(
            $apiOptions,
            $userOptions,
            $NotOptions,
        );

        // Is this model part of the options/allowed model? if not, then select the first one of the options
        if(isset($this->options['model']) && $this->verifyOption($this->options['model'], $sysOptions['model']['options']) == false){
            reset($sysOptions['model']['options']);
            $this->options['model'] = key($sysOptions['model']['options']);
        }
        if (isset($this->options['model']) && isset($sysOptions['model']['maxTokens'][$this->options['model']])) {
            $this->options['_model_maxTokens'] = $sysOptions['model']['maxTokens'][$this->options['model']];
        } else {
            $this->options['_model_maxTokens'] = 4097;
        }
        $this->debug('initOptions()', ['options' => $this->options]);
    }


    /**
     * Returns the option schema for the service.
     *
     * This will when user is creating a new API, it will be called from the Admin Portal to get the service's options
     * 
     * @param int|null $service_id
     * @param Model|null $serviceModel
     * @return array
     */

    public function getOptionSchema(?Model $serviceModel = null, ?int $service_id = null){
        $llmOptions = [];
        $llmMaxTokens = [];
        $defaultOptions = [];
        foreach($this->features as $featureProperty)
        {
            if(property_exists($this, $featureProperty)){
                $defaultOptions = array_merge($defaultOptions, static::${$featureProperty});
            }
        }
        if(isset($defaultOptions['model'])){
            if ($service_id !== null) {
                $service = ServiceModel::where(['id'=>$service_id, 'isActive'=>true ])->first();
                $llms = $service->llms()->get();
            }elseif ($serviceModel !== null) {
                $llms = $serviceModel->llms()->get();
            }
            foreach ($llms as $llm) {
                $llmOptions[$llm->modelName] = $llm->name;
                $llmMaxTokens[$llm->modelName] = $llm->maxTokens;
            }
            $defaultOptions['model']['options'] = $llmOptions;
            $defaultOptions['model']['maxTokens'] = $llmMaxTokens;
            $defaultOptions['model']['default'] = implode(',',array_keys($llmOptions));
        }

        $groupedOptions = [];
    
        foreach ($defaultOptions as $key => $option) {
            $defaultOptions[$key]['isApiOption'] = $option['isApiOption'] ?? 1;
            $defaultOptions[$key]['_allowApiOption'] = $option['_allowApiOption'] ?? 1;
            $defaultOptions[$key]['type'] = $option['type'] ?? 'Any';
            $group = isset($option["_group"]) ? $option["_group"] : 'General';
            $groupedOptions[$group][$key] = $option;
        }
        return $groupedOptions;
    }

    /**
     * Verifies if an option value is valid.
     *
     * @param mixed $optionValue
     * @param array $options
     * @return bool
     */
    private function verifyOption($optionValue, $options){
        return isset($options) && isset($options[$optionValue]);
    }

    protected function getMeta($serviceResponse){
        //if(isset($this->options['fakeLLM']) && $this->options['fakeLLM']) {
        if (!is_array($serviceResponse) && method_exists($serviceResponse, 'meta')) {
            $serviceMeta = $serviceResponse->meta();
        } else {
            $serviceMeta = [
                'requestId' => uniqid(),
                'requestLimit' => [
                    'limit' => 3500,
                    'remaining' => 3499,
                    'reset' => '17ms',
                ],
                'tokenLimit' => [
                    'limit' => 90000,
                    'remaining' => 89980,
                    'reset' => '12ms',
                ],
            ];
        }
        return $serviceMeta;
    }

    function getFileExtensionFromUrl($url, $default_extension = null) {
        $parsed_url = parse_url($url);
        $path = isset($parsed_url["path"]) ? $parsed_url["path"] : "";
        $file_extension = pathinfo($path, PATHINFO_EXTENSION);
        if ($file_extension) {
            return $file_extension;
        } else {
            return $default_extension;
        }
    }
    
}

