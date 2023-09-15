<?php

namespace App\AIEndPoints;


class OpenAIChatCompletionService extends Service
{
    
    protected static $REQUEST_SCHEMA = [

        ];
    
    protected $options;
    protected $openAIclient;
    protected $model;
    protected $sysOptions;
    protected $messages;
    protected $messagesMeta;
    protected $ChatCompletionOptions;
    protected $memoryKey;
    protected $memoryMetakey;

    public function __construct($options, $model){
        $this->options = $options;
        $this->model = $model;
        $this->sysOptions = [];
        foreach($this->model->requestSchema as $index => $item) {
            $this->sysOptions[$item['name']] = $item;
        }
    }


    private function init(){
        //TODO: manage App Token

        // $this->options['session'] = $this->options['session'] ?? 'global';
        // $this->options['openai_apiKey'] = $this->options['openai_apiKey'] ?? config('app.openai_api_key');
        // $this->options['openai_baseUri'] = $this->options['openai_baseUri'] ?? config('app.openai_base_uri');
        // $this->options['session'] = $this->options['session'] ?? 'global';
        // $this->options['model'] = $this->options['model'] ?? config('app.openai_default_chat_model');
        // $this->options['enable_memory'] = $this->options['enable_memory'] ?? 'disable';
        // $this->options['memory_period'] = $this->options['memory_period'] ?? 60;
        // $this->options['memory_max_token_percentage'] = $this->options['memory_max_token_percentage'] ?? 60;
        // $this->options['caching_period'] = $this->options['caching_period'] ?? 1440;
        // $sessionMD5 = md5($this->options['session']);

        // foreach(['openai_organization','system_message','stream','update_system_message','clear_memory','clear_cache','caching_scope','user_message','debug','messages','openai_apiVersion','max_tokens','temperature','top_p','n','stop','presence_penalty','frequency_penalty','user', 'logit_bias'] as $key){
        //     $this->options[$key] = $this->options[$key] ?? false;
        // }


        $cache = $this->getCache();
        if($cache){
            //TODO: if getCache not false then return the result!
        }


        $this->openAIclient = OpenAI::factory()->withApiKey($this->options['openai_apiKey'])->withBaseUri($this->options['openai_baseUri']);

        if($this->options['openai_apiVersion']){
            $this->openAIclient = $this->openAIclient->withQueryParam('api-version', $this->options['openai_apiVersion']);
        }
        if($this->options['openai_organization']){
            $this->openAIclient = $this->openAIclient->withOrganization('api-version', $this->options['openai_organization']);
        }
        if($this->options['stream']){
            //TODO: stram
            // $this->openAIclient = $this->openAIclient->withStreamHandler(fn (RequestInterface $request): ResponseInterface => $client->send($request, [
                // 'stream' => true // Allows to provide a custom stream handler for the http client.
            // ]));
        }
        $this->ChatCompletionOptions = [];
        foreach(['model','openai_apiVersion','max_tokens','temperature','top_p','n','stop','presence_penalty','frequency_penalty','user','logit_bias'] as $key){
            if($this->options[$key]) $ChatCompletionOptions[$key] = $this->options[$key];
        }
        $this->openAIclient = $this->openAIclient->make();
        
        $this->prepareMessages();

        $this->optimizeMemory();


    }

    public function execute(){
        $this->init();

        // TODO: saveMessages
        // TODO: save caching
        // TODO: debug
        // TODO: usage
        // TODO: enable_history 'embeddings' => 'Embeddings',
        // TODO: stream

        // $response = $this->openAIclient->chat()->create(
        //     array_merge(
        //         $this->ChatCompletionOptions,
        //         ['messages' => $this->messages],
        //     )
        // );
        // return $response->toArray();
    }
}
