<?php

namespace App\Services;

use App\Features\HasCaching;
use App\Features\LLMs\OpenAI\HasMessages;
use App\Features\LLMs\OpenAI\HasOpenAI;
use App\Features\LLMs\OpenAI\HasMemory;
use App\Features\HasDebug;

class OpenAIChatCompletionService extends BaseService
{
    use HasCaching;
    use HasMemory;
    use HasMessages;
    use HasOpenAI;
    use HasDebug;

    protected $features = [
        'cachingOptions',
        'openAIChatOptions',
        'memoryOptions',
        'messagesOptions',
        'debugOptions'
    ];

    protected $ApiModel;
    protected $api_id;
    protected $app_id;


    public function __construct($userOptions = [], $ApiModel = null, $httpRequest = null, $app = null){
        if($userOptions) {
            $this->ApiModel = $ApiModel;
            dd($this->ApiModel->collection_id);
            $this->api_id = $ApiModel->id;
            $this->app_id = $app->id ?? 0;
            $debug = [
                'requestOptions' => $userOptions
            ];
            if($httpRequest) $debug['header'] = $httpRequest->header();
            $this->initOptions($userOptions);

            $this->debug('input', $debug);
        }
    }

    public function execute(){
        $clearCache = false;
        $clearMemory = false;

        if($this->options['clearCache']) $clearCache = $this->clearCache();
        if($this->options['clearMemory']) $clearMemory = $this->clearMemory();

        if(!$this->options['userMessage'] && !$this->options['messages']) {
            if($clearCache) {
                return $this->responseMessage(['status' => true, 'message' => 'Cache cleared']);
            }elseif($clearMemory){
                return $this->responseMessage(['status' => true, 'message' => 'Memory cleared']);
            }
            return $this->responseMessage(['message' => 'No Message defined.']);
        }

        $message = $this->getCache();
        if($message) {
            return $this->responseMessage(['status' => true, 'message' => $message, 'cached'=>true, 'cacheScope' => $this->options['cacheScope']]);
        }

        $this->prepareMessages();
        $serviceResponse = $this->sendMessages();
        $this->saveMessages();
        $this->setCache();

        $serviceResponseMessage = null;
        if(count($serviceResponse->choices)==1){
            $serviceResponseMessage = [ 
                'role'=> $serviceResponse->choices[0]->message->role,
                'content'=> $serviceResponse->choices[0]->message->content, 
            ];
        }
        
        $response = [
            'status' => true, 
            'message' => $serviceResponseMessage, 
            'serviceResponse' => $serviceResponse, 
            'usage' => $this->usage
        ];

        if($this->options['returnMemory']) {
            $response['memory'] = $this->messages;
        }
        return $this->responseMessage($response);
    }

}
