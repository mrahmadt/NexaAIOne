<?php

namespace App\Services;

use App\Features\HasCaching;
use App\Features\LLMs\OpenAIChat\HasMessages;
use App\Features\LLMs\OpenAIChat\HasOpenAIChat;
use App\Features\LLMs\OpenAIChat\HasMemory;
use App\Features\HasDebug;

class OpenAIChatCompletionService extends BaseService
{
    use HasCaching;
    use HasMemory;
    use HasMessages;
    use HasOpenAIChat;
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
        try {
            $clearCache = false;
            $clearMemory = false;
            $this->__cacheInit([
                'userMessage',
                'openaiBaseUri',
                'openaiApiVersion',
                'openaiOrganization',
                'model',
                'max_tokens',
                'temperature',
                'top_p',
                'presence_penalty',
                'frequency_penalty',
                'logit_bias',
                'user',
            ]);

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

            $this->setCache(end($this->messages));
            // reset($this->messages);
            $serviceResponseMessage = null;
            if(!is_array($serviceResponse) && count($serviceResponse->choices)==1){
                $serviceResponseMessage = [ 
                    'role'=> $serviceResponse->choices[0]->message->role,
                    'content'=> $serviceResponse->choices[0]->message->content, 
                ];
            }

            $response = [
                'status' => true, 
                'message' => $serviceResponseMessage, 
                'serviceResponse' => $serviceResponse, 
                'serviceMeta' => $this->getMeta($serviceResponse), 
                'usage' => $this->usage
            ];
            if($this->options['returnMemory']) {
                $response['memory'] = $this->messages;
            }
        } catch (\Throwable $th) {
            $response = [
                'status' => false, 
                'message' => $th->getMessage(), 
                'code' => $th->getCode(), 
                'file' => $th->getFile(), 
                'line' => $th->getLine(), 
                'trace' => $th->getTrace(), 
            ];
        }

        return $this->responseMessage($response);
    }

}
