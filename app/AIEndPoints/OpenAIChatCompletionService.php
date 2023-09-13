<?php

namespace App\AIEndPoints;

use Illuminate\Support\Facades\Cache;
use OpenAI;
use Yethee\Tiktoken\EncoderProvider;

class OpenAIChatCompletionService extends AIEndPoint
{
    protected static $encoding_name = 'encoding_name'; // Encodings specify how text is converted into tokens https://github.com/openai/openai-cookbook/blob/main/examples/How_to_count_tokens_with_tiktoken.ipynb
    protected static $REQUEST_SCHEMA = [
            [
                "name" => "openai_baseUri",
                "type" => "string",
                "required" => false,
                "desc" => "OpenAI or Azure OpenAI baseUri (api.openai.com/v1 or {your-resource-name}.openai.azure.com/openai/deployments/{deployment-id}).",
                "default" => "api.openai.com/v1",
                "isApiOption" => false,
            ],
            [
                "name" => "openai_apiKey",
                "type" => "string",
                "required" => false,
                "desc" => "OpenAI or Azure OpenAI API Key (if not provided, will use the default API Key).",
                "default" => null,
                "isApiOption" => false,
            ],
            [
                "name" => "openai_apiVersion",
                "type" => "string",
                "required" => false,
                "desc" => "Azure OpenAI API Version.",
                "default" => null,
                "isApiOption" => false,
            ],
            [
                "name" => "openai_organization",
                "type" => "string",
                "required" => false,
                "desc" => "Azure OpenAI Your organization name.",
                "default" => null,
                "isApiOption" => false,
            ],
            [
                "name" => "model",
                "type" => "multiselect",
                "required" => false,
                "desc" => "The LLM model to use.",
            ],
            [
                "name" => "system_message",
                "type" => "text",
                "required" => false,
                "desc" => "Provides high-level directives or context for the entire conversation. Typically used at the beginning of the chat. If you wish to update or add more context during a conversation, use the 'update_system_message' parameter.",
                'default' => "You are a support agent. Provide brief and accurate answers. Elaborate only when prompted. If uncertain, admit you don't know. Shift between Friendly, Youthful, Funny, and Concise styles as needed.",
                "isApiOption" => false,
            ],
            [
                "name" => "update_system_message",
                "type" => "text",
                "required" => false,
                "desc" => "Allows you to send additional system-level instructions during a conversation, helping to refine or redirect the model's behavior as the conversation progresses.",
                "isApiOption" => false,
            ],
            [
                "name" => "session",
                "type" => "string",
                "required" => false,
                "desc" => "Unique session id for this conversation.",
                "default" => "global",
                "isApiOption" => true,
            ],
            [
                "name" => "caching_period",
                "type" => "number",
                "required" => false,
                "desc" => "How long should a message response be cached, in minutes? If the same message is submitted again, the response will be retrieved from the cache if available. Set to 0 to disable caching.",
                "default" => 1440,
                "isApiOption" => true,
            ],
            [
                "name" => "caching_scope",
                "type" => "select",
                "required" => false,
                "desc" => "Is the caching scope set per session, or is it global (across all sessions)? set 'session' for individual session-based caching or 'global' for caching that spans across all sessions.",
                "default" => "session",
                "isApiOption" => false,
                "options"=>[
                    'session' => 'Per Session',
                    'global' => 'Global'
                ]
            ],
            [
                "name" => "clear_cache",
                "type" => "boolean",
                "required" => false,
                "desc" => "Clear cache for the specified user_message and return an answer.",
                "isApiOption" => true,

            ],
            [
                "name" => "enable_memory",
                "type" => "select",
                "required" => false,
                "desc" => "Do you want to enable conversation tracking? Turning this on will retain a record of past conversations.",
                "default" => true,
                "isApiOption" => false,
                "options"=>[
                    'disable' => 'Disable',
                    'noOptimization' => 'No memory optimization',
                    'truncate' => 'Truncate',
                    'summary' => 'Summary',
                    'embeddings' => 'Embeddings',
                ]
            ],
            [
                "name" => "memory_period",
                "type" => "number",
                "required" => false,
                "desc" => "How long, in minutes, should the conversation be retained in memory? If no new messages are received within this duration, the conversation history will be cleared",
                "default" => 60,
                "isApiOption" => false,
            ],
            [
                "name" => "memory_max_token_percentage",
                "type" => "number",
                "required" => false,
                "desc" => "Defines the threshold, as a percentage of the GPT Model's max tokens, at which memory optimization will be triggered. When memory token usage reaches this specified percentage, optimization measures specified in the enable_memory variable will be enacted.",
                "default" => 60,
                "isApiOption" => false,
            ],
            [
                "name" => "clear_memory",
                "type" => "boolean",
                "required" => false,
                "desc" => "Clear memory for this session and return an answer for user_message.",
                "isApiOption" => true,

            ],
            [
                "name" => "user_message",
                "type" => "string",
                "required" => true,
                "desc" => "Instruct, question, or guide the model, eliciting specific responses based on the input provided.",
                "isApiOption" => true,
            ],
            [
                "name" => "max_tokens",
                "type" => "number",
                "required" => false,
                "desc" => "The maximum number of tokens to generate in the chat completion.

                The total length of input tokens and generated tokens is limited by the model's context length"
            ],
            [
                "name" => "stream",
                "type" => "boolean",
                "required" => false,
                "desc" => "If set, partial message deltas will be sent, like in ChatGPT. Tokens will be sent as data-only server-sent events as they become available, with the stream terminated by a data: [DONE] message.",
                "default" => false,
            ],
            [
                "name" => "debug",
                "type" => "boolean",
                "required" => false,
                "desc" => "Retains all request and response data, facilitating issue troubleshooting and prompt optimization",
                "default" => false,
            ],
            [
                "name" => "messages",
                "type" => "json",
                "required" => false,
                "desc" => "A list of messages comprising the conversation so far. check https://platform.openai.com/docs/api-reference/chat/create#messages",
                "isApiOption" => false,
            ],
            [
            "name" => "temperature",
            "type" => "number",
            "required" => false,
            "desc" => "What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic.

We generally recommend altering this or top_p but not both.",
            "default" => 1
            ],
            [
                "name" => "top_p",
                "type" => "number",
                "required" => false,
                "desc" => "An alternative to sampling with temperature, called nucleus sampling, where the model considers the results of the tokens with top_p probability mass. So 0.1 means only the tokens comprising the top 10% probability mass are considered.

            We generally recommend altering this or temperature but not both.",
                "default" => 1
            ],
            [
                "name" => "n",
                "type" => "number",
                "required" => false,
                "desc" => "How many chat completion choices to generate for each input message.",
                "default" => 1,
            ],
            [
                "name" => "stop",
                "type" => "string / array / null",
                "required" => false,
                "desc" => "Up to 4 sequences where the API will stop generating further tokens.", 
                "default" => null,
            ],
            [
                "name" => "presence_penalty",
                "type" => "number", 
                "required" => false, 
                "desc" => "Number between -2.0 and 2.0. Positive values penalize new tokens based on whether they appear in the text so far, increasing the model's likelihood to talk about new topics.", 
                "default" => 0
            ],
            [
                "name" => "frequency_penalty",
                "type" => "number", 
                "required" => false, 
                "desc" => "Number between -2.0 and 2.0. Positive values penalize new tokens based on their existing frequency in the text so far, decreasing the model's likelihood to repeat the same line verbatim.",
                "default" => 0
            ],
            [
                "name" => "logit_bias",
                "type" => "json", 
                "required" => false,
                "desc" => "Modify the likelihood of specified tokens appearing in the completion.
                Accepts a json object that maps tokens (specified by their token ID in the tokenizer) to an associated bias value from -100 to 100. Mathematically, the bias is added to the logits generated by the model prior to sampling. The exact effect will vary per model, but values between -1 and 1 should decrease or increase likelihood of selection; values like -100 or 100 should result in a ban or exclusive selection of the relevant token.",
                "default" => null
            ],
            [
                "name" => "user",
                "type" => "string",
                "required" => false, 
                "desc" => "A unique identifier representing your end-user, which can help OpenAI to monitor and detect abuse.", 
                "default" => null,
                "isApiOption" => true,
            ],

        ];
    
    protected $options;
    protected $openAIclient;
    protected $model;
    protected $sysOptions;
    protected $messages;
    protected $messagesMeta;
    protected $ChatCompletionOptions;
    protected $cacheKey;
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
    public function getMaxTokensForModel() {
        $model = $this->options['model'];
        $index = array_search($model, $this->sysOptions['model']['options']);
        if ($index !== false) {
            return $this->sysOptions['model']['maxTokens'][$index];
        } else {
            return 4097;
        }
    }

    private function VerifyOption($optionName, $optionValue){
        return isset($this->sysOptions[$optionName]['options']) && isset($this->sysOptions[$optionName]['options'][$optionValue]);
    }

    private function getCache(){
        if($this->options['clear_cache']){ return false; }

        if ($this->options['caching_period'] && $this->options['user_message'] ){
            $this->cacheKey = 'msg:' . md5($this->options['user_message']);
            if($this->options['caching_scope'] == 'session') $this->cacheKey = $this->options['session'] . ':' . $this->cacheKey;
                if(Cache::has($this->cacheKey)) {
                    $value = Cache::get($this->cacheKey);
                    $minutes = $this->options['caching_period'] / 60;
                    Cache::put($this->memoryKey, $value, $minutes);
                    return $value;
                }
        }
        return false;
    }

    private function saveMessages(){
        if ($this->options['enable_memory'] != 'disable'){
            $minutes = $this->options['memory_period'] / 60;
            Cache::put($this->memoryKey, json_encode($this->messages), $minutes);
            Cache::put($this->memoryKey, json_encode($this->messagesMeta), $minutes);
        }
    }
    private function getMessages(){
        $this->messages = [];
        $this->memoryMetakey = [];
        if($this->options['clear_memory']){
            $this->messages = [];
            $this->memoryMetakey = [];
            //Cache::forget($this->memoryKey);
        }elseif ($this->options['enable_memory'] != 'disable'){
            $minutes = $this->options['memory_period'] / 60;
            $memoryMetakey_value = Cache::get($this->memoryMetakey);
            $memoryKey_value = Cache::get($this->memoryKey);
            Cache::put($this->memoryMetakey, $memoryMetakey_value, $minutes);
            Cache::put($this->memoryKey, $memoryKey_value, $minutes);
            $this->memoryMetakey = json_decode($memoryMetakey_value);
            $this->messages = json_decode($memoryKey_value);
        }
    }

    private function addMessage($message, $tokens = false){
        if ($this->options['enable_memory'] != 'disable' && $this->options['enable_memory'] != 'noOptimization'){
            if(!$tokens){
                $provider = new EncoderProvider();
                $encoder = $provider->get(self::$encoding_name);
                $tokens = $encoder->encode($message['content']);
                $tokens = count($tokens);
            }
            $this->messagesMeta[]['tokens'] = $tokens;
            if(!isset($this->messagesMeta['all']['totalTokens'])) $this->messagesMeta['all']['totalTokens'] = 0;
            $this->messagesMeta['all']['totalTokens']+= $tokens;
        }
        $this->messages[] = $message;
    }

    private function prepareMessages(){
        if($this->options['messages']) {
            $this->messages = $this->options['messages'];
            $this->options['user_message'] = false;
            return true;
        }

        $this->getMessages();

        if($this->messages){
            if($this->options['update_system_message']){
                $this->addMessage(['role' => 'system', 'content' => $this->options['update_system_message']]);
            }

        }else{
            foreach ($this->options as $key => $value) {
                $this->options['system_message'] = str_replace('{{'.$key.'}}', $value, $this->options['system_message']);
            }
            if($this->options['system_message']){
                $this->addMessage(['role' => 'system', 'content' => $this->options['system_message']]);
            }
        }
        $this->addMessage(['role' => 'user', 'content' => $this->options['user_message'] || "Say Hello"]);
        return true;
    }

    private function optimizeMemory(){
        if ($this->options['enable_memory'] == 'disable' || $this->options['enable_memory'] == 'noOptimization') return false;
        // Calculate % of model Max Tokens
        $modelMaxTokens = $this->getMaxTokensForModel();;
        $historyMaxTokens = ($this->options['memory_max_token_percentage']/100) * $modelMaxTokens;
        if ($this->messagesMeta['all']['totalTokens'] >= $historyMaxTokens) {
            $lastMessage = array_pop($this->messages);
            $lastMessageMeta = array_pop($this->messagesMeta);
            if ($this->options['enable_memory'] == 'truncate'){
                $this->optimizeMemoryTruncate($historyMaxTokens);
            }elseif ($this->options['enable_memory'] == 'summary'){
                $this->optimizeMemorySummary($historyMaxTokens);
            }elseif ($this->options['enable_memory'] == 'embeddings'){

            }
            $this->messages[] = $lastMessage;
            $this->messagesMeta[] = $lastMessageMeta;
            $this->messagesMeta['all']['totalTokens'] = 0;
            foreach($this->messagesMeta as $meta) {
                if(isset($meta['tokens'])) {
                    $this->messagesMeta['all']['totalTokens'] += $meta['tokens'];
                }
            }

        }
    }

    private function optimizeMemorySummary($historyMaxTokens) {
        $prompt_system = [ 'role'=> 'system', 'content'=> config('app.openai_summarization_prompt') ];
        $totalWords = ($historyMaxTokens / 4) . ' words';
        $totalTokens = $historyMaxTokens . ' tokens';
        $prompt_system['content'] = str_replace(['{{totalTokens}}','{{totalWords}}'], [$totalTokens,$totalWords], $prompt_system['content']);

        $text_user = null;
        foreach($this->messages as $index => $message) {
            if($message['role'] == 'user'){
                $text_user .= 'User:' . $message['content'] . "\n";
            }elseif($message['role'] == 'assistant'){
                $text_user .= 'AI:' . $message['content'] . "\n";
            }
        }
        $prompt_user = [ 'role'=> 'user', 'content'=> $text_user ];

        $ChatCompletionOptions = $this->ChatCompletionOptions;
        $ChatCompletionOptions['model'] = config('app.openai_summarization_model') ?? $ChatCompletionOptions['model'];
        $response = $this->sendMessages([$prompt_system, $prompt_user], $ChatCompletionOptions);

        
        $system_index = null;
        foreach (array_reverse($this->messages, true) as $index => $message) {
            if ($message['role'] === 'system') {
                $system_index = $index;
                break;
            }
        }
        $messages = [];
        $messagesMeta = [];
        if(!is_null($system_index)) { 
            $messages[] = $this->messages[$system_index];
            $messagesMeta[] = $this->messagesMeta[$system_index];
        }

        $messages[] = ['role'=>'assistant', 'content'=> $response->choices[0]->message->content];
        $messagesMeta[] = [ 'tokens'=> $response->usage->completionTokens];

        $this->messages = $messages;
        $this->messagesMeta = $messagesMeta;
    }

    private function optimizeMemoryTruncate($historyMaxTokens) {
        $tokensCounted = 0;
        $messagesToKeep = [];
        $messagesMetaToKeep = [];
        $lastSystemMessageIndex = null;
        $handleMessage = function($index, $message) use (&$tokensCounted, &$messagesToKeep, &$messagesMetaToKeep) {
            $messagesToKeep[] = $message;
            $messagesMetaToKeep[] = $this->messagesMeta[$index];
            $tokensCounted += $this->messagesMeta[$index]['tokens'];
        };
        foreach (array_reverse($this->messages, true) as $index => $message) {
            if ($tokensCounted < $historyMaxTokens) {
                if(is_null($lastSystemMessageIndex) && $message['role'] == 'system') $lastSystemMessageIndex = $index;
                $handleMessage($index, $message);
            } else {
                break;
            }
        }
        if(is_null($lastSystemMessageIndex)){
            foreach (array_reverse($this->messages, true) as $index => $message) {
                if ($message['role'] === 'system') {
                    $handleMessage($index, $message);
                    break;
                }
            }
        }
        $this->messages = array_reverse($messagesToKeep);
        $this->messagesMeta = array_reverse($messagesMetaToKeep);
    }

    private function init(){
        //TODO: manage App Token

        $this->options['session'] = $this->options['session'] ?? 'global';
        $this->options['openai_apiKey'] = $this->options['openai_apiKey'] ?? config('app.openai_api_key');
        $this->options['openai_baseUri'] = $this->options['openai_baseUri'] ?? config('app.openai_base_uri');
        $this->options['session'] = $this->options['session'] ?? 'global';
        $this->options['model'] = $this->options['model'] ?? config('app.openai_default_chat_model');
        $this->options['enable_memory'] = $this->options['enable_memory'] ?? 'disable';
        $this->options['memory_period'] = $this->options['memory_period'] ?? 60;
        $this->options['memory_max_token_percentage'] = $this->options['memory_max_token_percentage'] ?? 60;

        $this->options['caching_period'] = $this->options['caching_period'] ?? 1440;
        $sessionMD5 = md5($this->options['session']);
        $this->memoryKey = 'mem:' . $sessionMD5;
        $this->memoryMetakey = 'memMeta:' . $sessionMD5;

        foreach(['openai_organization','system_message','stream','update_system_message','clear_memory','clear_cache','caching_scope','user_message','debug','messages','openai_apiVersion','max_tokens','temperature','top_p','n','stop','presence_penalty','frequency_penalty','user', 'logit_bias'] as $key){
            $this->options[$key] = $this->options[$key] ?? false;
        }


        $cache = $this->getCache();
        if($cache){
            //TODO: if getCache not false then return the result!
        }

        // Is this model part of the options/allowed model? if not, then select the first one of the options
        if($this->VerifyOption('model', $this->options['model']) == false){
            reset($this->sysOptions['model']['options']);
            $this->options['model'] = key($this->sysOptions['model']['options']);
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
    private function sendMessages($messages = [], $ChatCompletionOptions = [], $addToMessages = true){
        $response = $this->openAIclient->chat()->create(
            array_merge(
                $ChatCompletionOptions || $this->ChatCompletionOptions,
                ['messages' => $messages || $this->messages],
            )
        );
        if($addToMessages){
            if(count($response->choices)>1){
                foreach ($response->choices as $result) {
                    $message = [ 
                        'role'=> $result->message->role,
                        'content'=> $result->message->content, 
                    ];
                    $this->addMessage($message);
                }
            }else{
                $message = [ 
                    'role'=> $response->choices[0]->message->role,
                    'content'=> $response->choices[0]->message->content, 
                ];
                $this->addMessage($message, $response->usage->completionTokens);
            }
        }
        return $response;
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
