<?php

namespace App\Features;
use App\Models\Api;

trait HasOptions
{
    protected $options = [];
    protected $ApiModel;
    protected $sysOptions = [];
    protected $defaultOptions = [
        "debug" => [
            "name" => "debug",
            "type" => "boolean",
            "required" => false,
            "desc" => "Retains all request and response data, facilitating issue troubleshooting and prompt optimization",
            "default" => false,
        ],
    ];
    private function initOptions($userOptions){
        foreach([
            'cachingOptions',
            'HasOpenAIChat',
            'memoryOptions',
            'messagesOptions',
        ] as $featureProperty)
        {
            if(property_exists($this, $featureProperty)){
                $this->defaultOptions = array_merge($this->defaultOptions, self::${$featureProperty});
            }
        }

        $apiOptions=[];
        $NotOptions=[];
        foreach($this->ApiModel->options as $index => $item){
            if(isset($item['isApiOption']) && $item['isApiOption']){
                $apiOptions[$item['name']] = $item['default'] ?? $this->defaultOptions[$item['name']]['default'];
            }elseif(( !isset($item['isApiOption']) || $item['isApiOption'] == false )){
                $NotOptions[$item['name']] = $item['default'] ?? $this->defaultOptions[$item['name']]['default'];
            }
            $this->sysOptions[$item['name']] = $item;
        }

        $this->options = array_merge(
            $this->apiOptions,
            $userOptions,
            $this->NotOptions,
        );

        // Is this model part of the options/allowed model? if not, then select the first one of the options
        if(isset($this->options['model']) && $this->verifyOption('model', $this->options['model']) == false){
            reset($this->sysOptions['model']['options']);
            $this->options['model'] = key($this->sysOptions['model']['options']);
        }
        
    }

    private function verifyOption($optionName, $optionValue){
        return isset($this->sysOptions[$optionName]['options']) && isset($this->sysOptions[$optionName]['options'][$optionValue]);
    }

    public function getMaxTokensForModel() {
        if (isset($this->sysOptions['model']['maxTokens'][$this->options['model']])) {
            return $this->sysOptions['model']['maxTokens'][$this->options['model']];
        } else {
            return 4097;
        }
    }

}