<?php

namespace App\Features;

use OpenAI;

trait HasOpenAIChat
{
    use HasOptions;
    protected $LLMclient;
    protected $LLMclientNoSteam;
    protected $ChatCompletionOptions;

    protected static $openAIOptions = [
        'openaiBaseUri' => [
            "name" => "openaiBaseUri",
            "type" => "string",
            "required" => false,
            "desc" => "OpenAI or Azure OpenAI baseUri (api.openai.com/v1 or {your-resource-name}.openai.azure.com/openai/deployments/{deployment-id}).",
            "default" => "api.openai.com/v1",
            "isApiOption" => false,
        ],
        'openaiApiKey' => [
            "name" => "openaiApiKey",
            "type" => "string",
            "required" => false,
            "desc" => "OpenAI or Azure OpenAI API Key (if not provided, will use the default API Key).",
            "default" => null,
            "isApiOption" => false,
        ],
        'openaiApiVersion' => [
            "name" => "openaiApiVersion",
            "type" => "string",
            "required" => false,
            "desc" => "Azure OpenAI API Version.",
            "default" => null,
            "isApiOption" => false,
        ],
        'openaiOrganization' => [
            "name" => "openaiOrganization",
            "type" => "string",
            "required" => false,
            "desc" => "Azure OpenAI Your organization name.",
            "default" => null,
            "isApiOption" => false,
        ],
        'model' => [
            "name" => "model",
            "type" => "multiselect",
            "required" => false,
            "desc" => "The LLM model to use.",
            "default" => "gpt-3.5-turbo",
        ],
        'max_tokens' => [
            "name" => "max_tokens",
            "type" => "number",
            "required" => false,
            "desc" => "The maximum number of tokens to generate in the chat completion.

            The total length of input tokens and generated tokens is limited by the model's context length",
            "default" => null,
        ],
        'stream' => [
            "name" => "stream",
            "type" => "boolean",
            "required" => false,
            "desc" => "If set, partial message deltas will be sent, like in ChatGPT. Tokens will be sent as data-only server-sent events as they become available, with the stream terminated by a data: [DONE] message.",
            "default" => false,
        ],
        'temperature' => [
        "name" => "temperature",
        "type" => "number",
        "required" => false,
        "desc" => "What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic.

We generally recommend altering this or top_p but not both.",
        "default" => 1
        ],
        'top_p' => [
            "name" => "top_p",
            "type" => "number",
            "required" => false,
            "desc" => "An alternative to sampling with temperature, called nucleus sampling, where the model considers the results of the tokens with top_p probability mass. So 0.1 means only the tokens comprising the top 10% probability mass are considered.

        We generally recommend altering this or temperature but not both.",
            "default" => 1
        ],
        'n' => [
            "name" => "n",
            "type" => "number",
            "required" => false,
            "desc" => "How many chat completion choices to generate for each input message.",
            "default" => 1,
        ],
        'stop' => [
            "name" => "stop",
            "type" => "string / array / null",
            "required" => false,
            "desc" => "Up to 4 sequences where the API will stop generating further tokens.", 
            "default" => null,
        ],
        'presence_penalty' => [
            "name" => "presence_penalty",
            "type" => "number", 
            "required" => false, 
            "desc" => "Number between -2.0 and 2.0. Positive values penalize new tokens based on whether they appear in the text so far, increasing the model's likelihood to talk about new topics.", 
            "default" => 0
        ],
        'frequency_penalty' => [
            "name" => "frequency_penalty",
            "type" => "number", 
            "required" => false, 
            "desc" => "Number between -2.0 and 2.0. Positive values penalize new tokens based on their existing frequency in the text so far, decreasing the model's likelihood to repeat the same line verbatim.",
            "default" => 0
        ],
        'logit_bias' => [
            "name" => "logit_bias",
            "type" => "json", 
            "required" => false,
            "desc" => "Modify the likelihood of specified tokens appearing in the completion.
            Accepts a json object that maps tokens (specified by their token ID in the tokenizer) to an associated bias value from -100 to 100. Mathematically, the bias is added to the logits generated by the model prior to sampling. The exact effect will vary per model, but values between -1 and 1 should decrease or increase likelihood of selection; values like -100 or 100 should result in a ban or exclusive selection of the relevant token.",
            "default" => null
        ],
        'user' => [
            "name" => "user",
            "type" => "string",
            "required" => false, 
            "desc" => "A unique identifier representing your end-user, which can help OpenAI to monitor and detect abuse.", 
            "default" => null,
            "isApiOption" => true,
        ],

    ];

    private function setupLLM(){
        $this->ChatCompletionOptions = [];
        foreach(['model','openaiApiVersion','max_tokens','temperature','top_p','n','stop','presence_penalty','frequency_penalty','user','logit_bias'] as $key){
            if($this->options[$key]) $ChatCompletionOptions[$key] = $this->options[$key];
        }

        $this->LLMclient = OpenAI::factory()->withApiKey($this->options['openaiApiKey'])->withBaseUri($this->options['openaiBaseUri']);

        if($this->options['openaiApiVersion']){
            $this->LLMclient = $this->LLMclient->withQueryParam('api-version', $this->options['openaiApiVersion']);
        }
        if($this->options['openaiOrganization']){
            $this->LLMclient = $this->LLMclient->withOrganization('api-version', $this->options['openaiOrganization']);
        }
        
        $this->LLMclientNoSteam = $this->LLMclientNoSteam->make();

        if($this->options['stream']){
            //TODO: stram
            // $this->LLMclient = $this->LLMclient->withStreamHandler(fn (RequestInterface $request): ResponseInterface => $client->send($request, [
                // 'stream' => true // Allows to provide a custom stream handler for the http client.
            // ]));
        }
        
        $this->LLMclient = $this->LLMclient->make();
    }

    private function sendMessageToLLM($messages = [], $ChatCompletionOptions = [], $noStreamClient = false){
        $LLMclient = ($noStreamClient) ? $this->LLMclientNoSteam : $this->LLMclient;
        $response = $LLMclient->chat()->create(
            array_merge(
                $ChatCompletionOptions || $this->ChatCompletionOptions,
                ['messages' => $messages || $this->messages],
            )
        );
        return $response;
    }
}