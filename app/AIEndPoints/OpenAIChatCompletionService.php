<?php

namespace App\AIEndPoints;

use Filament\Notifications\Auth\VerifyEmail;
use OpenAI;
class OpenAIChatCompletionService extends AIEndPoint
{
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
                "name" => "preSystemMessage",
                "type" => "text",
                "required" => false,
                "desc" => "An optional instruction or context provided prior to the main system message (will be used only if system_message provided as API option), setting the foundational tone or guidelines for the ensuing conversation",
                "_allowApiOption" => false,
            ],
            [
                "name" => "postSystemMessage",
                "type" => "text",
                "required" => false,
                "desc" => "An optional instruction or context appended after the main system message (will be used only if system_message provided as API option), used to fine-tune or further direct the model's responses in the conversation.",
                "_allowApiOption" => false,
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
                "isApiOption" => false,
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
                "name" => "enable_history",
                "type" => "select",
                "required" => false,
                "desc" => "Do you want to enable conversation history tracking? Turning this on will retain a record of past conversations.",
                "default" => true,
                "isApiOption" => false,
                "options"=>[
                    'disable' => 'Disable',
                    'truncate' => 'Truncate',
                    'summary' => 'Summary',
                    'embeddings' => 'Embeddings',
                ]
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
                "desc" => "The maximum number of tokens to generate in the chat completion."
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
            ]
        ];
    
    protected $options;
    protected $openAIclient;
    protected $model;
    protected $sysOptions;
    protected $messages;
    protected $ChatCompletionOptions;

    public function __construct($options, $model){
        $this->options = $options;
        $this->model = $model;
        $this->sysOptions = [];
        foreach($this->model->requestSchema as $index => $item) {
            $this->sysOptions[$item['name']] = $item;
        }
    }
    private function VerifyOption($optionName, $optionValue){
        if(isset($this->sysOptions[$optionName]['options']) && isset($this->sysOptions[$optionName]['options'][$optionValue])){
            return true;
        }
        return false;
    }
    private function initOpenAI(){
        $this->options['openai_apiKey'] = $this->options['openai_apiKey'] ?? config('app.openai_api_key');
        $this->options['openai_baseUri'] = $this->options['openai_baseUri'] ?? config('app.openai_base_uri');
        $this->options['session'] = $this->options['session'] ?? 'global';
        $this->options['enable_history'] = $this->options['enable_history'] ?? false;
        $this->options['model'] = $this->options['model'] ?? config('app.openai_default_chat_model');

        foreach(['enable_history','debug','messages','openai_apiVersion','max_tokens','temperature','top_p','n','stop','presence_penalty','frequency_penalty','user', 'logit_bias'] as $key){
            $this->options[$key] = $this->options[$key] ?? false;
        }

        if($this->VerifyOption('model', $this->options['model']) == false){
            reset($this->sysOptions['model']['options']);
            $this->options['model'] = key($this->sysOptions['model']['options']);
        }

        $this->openAIclient = OpenAI::factory()->withApiKey($this->options['openai_apiKey'])->withBaseUri($this->options['openai_baseUri']);

        if($this->options['openai_apiVersion']){
            $this->openAIclient = $this->openAIclient->withQueryParam('api-version', $this->options['openai_apiVersion']);
        }
        $this->openAIclient = $this->openAIclient->make();
        // ->withStreamHandler(fn (RequestInterface $request): ResponseInterface => $client->send($request, [
            // 'stream' => true // Allows to provide a custom stream handler for the http client.
        // ]))

        $this->ChatCompletionOptions = [
            'model' => $this->options['model'],
        ];
        
        foreach(['openai_apiVersion','max_tokens','temperature','top_p','n','stop','presence_penalty','frequency_penalty','user','logit_bias'] as $key){
            if($this->options[$key]) $ChatCompletionOptions[$key] = $this->options[$key];
        }

        if($this->options['messages']) {
            $this->messages = $this->options['messages'];
        }

        $response = $this->openAIclient->chat()->create([
            'messages' => [
                ['role' => 'user', 'content' => 'Give me 5 male baby names'],
            ],
        ]);

        //TODO: check logit_bias      json
        //TODO: check stop      string / array / null

        //caching

        //debug

        //usage

        //history

        //api 

        /*
        user_message
        stream
        debug

        "name" => "enable_history",
        "options"=>[
            'disable' => 'Disable',
            'truncate' => 'Truncate',
            'summary' => 'Summary',
            'embeddings' => 'Embeddings',
        ]
        */


    }

    public function execute(){
        $this->initOpenAI();
        $response = $this->openAIclient->chat()->create([
            'model' => $this->options['model'],
            'messages' => [
                ['role' => 'user', 'content' => 'Give me 5 male baby names'],
            ],
        ]);
        return $response->toArray();
    }
}
