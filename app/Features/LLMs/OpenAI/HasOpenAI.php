<?php
namespace App\Features\LLMs\OpenAI;

use OpenAI;
use Faker\Factory;

trait HasOpenAI
{
    /**
     * An instance of the OpenAI API client.
     *
     * @var Client
     */
    protected $LLMclient;

    /**
     * A boolean that indicates whether the OpenAI API client is ready to use.
     *
     * @var bool
     */
    protected $LLMReady = false;

    /**
     * The options for the chat completion.
     *
     * @var array
     */

    protected $ChatCompletionOptions;

    
    protected static $openAIChatOptions = [
        'openaiBaseUri' => [
            "name" => "openaiBaseUri",
            "type" => "string",
            "required" => false,
            "desc" => "OpenAI or Azure OpenAI baseUri (api.openai.com/v1 or {your-resource-name}.openai.azure.com/openai/deployments/{deployment-id}).",
            "default" => "api.openai.com/v1",
            "isApiOption" => false,
            "_group" => 'OpenAI',
        ],
        'openaiApiKey' => [
            "name" => "openaiApiKey",
            "type" => "string",
            "required" => false,
            "desc" => "OpenAI or Azure OpenAI API Key (if not provided, will use the default API Key in the system).",
            "default" => null,
            "isApiOption" => false,
            "_group" => 'OpenAI',
        ],
        'openaiApiVersion' => [
            "name" => "openaiApiVersion",
            "type" => "string",
            "required" => false,
            "desc" => "Azure OpenAI API Version.",
            "default" => null,
            "isApiOption" => false,
            "_group" => 'OpenAI',
        ],
        'openaiOrganization' => [
            "name" => "openaiOrganization",
            "type" => "string",
            "required" => false,
            "desc" => "Azure OpenAI Your organization name.",
            "default" => null,
            "isApiOption" => false,
            "_group" => 'OpenAI',
        ],
        'model' => [
            "name" => "model",
            "type" => "multiselect",
            "required" => false,
            "desc" => "The LLM model to use.",
            "default" => "gpt-3.5-turbo",
            "isApiOption" => false,
            "_group" => 'OpenAI',
        ],
        'max_tokens' => [
            "name" => "max_tokens",
            "type" => "number",
            "required" => false,
            "desc" => "The maximum number of tokens to generate in the chat completion.

            The total length of input tokens and generated tokens is limited by the model's context length",
            "default" => null,
            "isApiOption" => false,
            "_group" => 'OpenAI',
        ],
        'stream' => [
            "name" => "stream",
            "type" => "boolean",
            "required" => false,
            "desc" => "If set, partial message deltas will be sent, like in ChatGPT. Tokens will be sent as data-only server-sent events as they become available, with the stream terminated by a data: [DONE] message.",
            "default" => 0,
            "isApiOption" => false,
            "_group" => 'OpenAI',
        ],
        'temperature' => [
        "name" => "temperature",
        "type" => "number",
        "required" => false,
        "desc" => "What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic.

We generally recommend altering this or top_p but not both.",
        "default" => 1,
        "isApiOption" => false,
        "_group" => 'OpenAI',
        ],
        'top_p' => [
            "name" => "top_p",
            "type" => "number",
            "required" => false,
            "desc" => "An alternative to sampling with temperature, called nucleus sampling, where the model considers the results of the tokens with top_p probability mass. So 0.1 means only the tokens comprising the top 10% probability mass are considered.

        We generally recommend altering this or temperature but not both.",
            "default" => 1,
            "isApiOption" => false,
            "_group" => 'OpenAI',
        ],
        'n' => [
            "name" => "n",
            "type" => "number",
            "required" => false,
            "desc" => "How many chat completion choices to generate for each input message.",
            "default" => 1,
            "isApiOption" => false,
        ],
        'stop' => [
            "name" => "stop",
            "type" => "string / array / null",
            "required" => false,
            "desc" => "Up to 4 sequences where the API will stop generating further tokens.",
            "default" => null,
            "isApiOption" => false,
            "_group" => 'OpenAI',
        ],
        'presence_penalty' => [
            "name" => "presence_penalty",
            "type" => "number",
            "required" => false,
            "desc" => "Number between -2.0 and 2.0. Positive values penalize new tokens based on whether they appear in the text so far, increasing the model's likelihood to talk about new topics.",
            "default" => 0,
            "isApiOption" => false,
            "_group" => 'OpenAI',
        ],
        'frequency_penalty' => [
            "name" => "frequency_penalty",
            "type" => "number",
            "required" => false,
            "desc" => "Number between -2.0 and 2.0. Positive values penalize new tokens based on their existing frequency in the text so far, decreasing the model's likelihood to repeat the same line verbatim.",
            "default" => 0,
            "isApiOption" => false,
            "_group" => 'OpenAI',
        ],
        'logit_bias' => [
            "name" => "logit_bias",
            "type" => "json",
            "required" => false,
            "desc" => "Modify the likelihood of specified tokens appearing in the completion.
            Accepts a json object that maps tokens (specified by their token ID in the tokenizer) to an associated bias value from -100 to 100. Mathematically, the bias is added to the logits generated by the model prior to sampling. The exact effect will vary per model, but values between -1 and 1 should decrease or increase likelihood of selection; values like -100 or 100 should result in a ban or exclusive selection of the relevant token.",
            "default" => null,
            "isApiOption" => false,
            "_group" => 'OpenAI',
        ],
        'user' => [
            "name" => "user",
            "type" => "string",
            "required" => false,
            "desc" => "A unique identifier representing your end-user, which can help OpenAI to monitor and detect abuse.",
            "default" => null,
            "isApiOption" => false,
            "_group" => 'OpenAI',
        ],
        'fakeLLM' => [
            "name" => "fakeLLM",
            "type" => "boolean",
            "required" => false,
            "desc" => "If set, will return a fake LLM response.",
            "default" => 0,
            "isApiOption" => true,
            "_group" => 'OpenAI',
        ],
        'fakeLLMOutput' => [
            "name" => "fakeLLMOutput",
            "type" => "string / OpenAI Chat Completion Response in JSON",
            "required" => false,
            "desc" => "You can define the fake LLM response or it will return random string.",
            "default" => null,
            "isApiOption" => true,
            "_group" => 'OpenAI',
        ],
        
    ];

    /**
     * Set up the OpenAI API client.
     *
     * @return void
     */
    private function setupLLM()
    {
        $this->LLMReady = true;
        $this->ChatCompletionOptions = [];
        if(!isset($this->options['openaiApiKey'])) {
            $this->options['openaiApiKey'] = config('openai.api_key');
        }
        foreach(['model','openaiApiVersion','max_tokens','temperature','top_p','n','stop','presence_penalty','frequency_penalty','user','logit_bias'] as $key) {
            if($this->options[$key]) {
                $this->ChatCompletionOptions[$key] = $this->options[$key];
            }
        }

        $this->LLMclient = OpenAI::factory()->withApiKey($this->options['openaiApiKey'])->withBaseUri($this->options['openaiBaseUri']);

        if($this->options['openaiApiVersion']) {
            $this->LLMclient = $this->LLMclient->withQueryParam('api-version', $this->options['openaiApiVersion']);
        }
        if($this->options['openaiOrganization']) {
            $this->LLMclient = $this->LLMclient->withOrganization($this->options['openaiOrganization']);
        }
        
        $this->debug('setupLLM()', ['ChatCompletionOptions' => $this->ChatCompletionOptions]);

        $this->LLMclient = $this->LLMclient->make();
    }

    /**
     * Generate a fake response from the OpenAI API.
     *
     * @param array $ChatCompletionOptions
     * @return mixed
     */
    private function sendMessageToFakeLLM($ChatCompletionOptions = [])
    {
        $responseMessageContent = null;
        if(isset($this->options['fakeLLMOutput']) && $this->options['fakeLLMOutput']) {
            if(json_decode($this->options['fakeLLMOutput'])) {
                $jsonResponse = $this->options['fakeLLMOutput'];
                return json_decode($jsonResponse);
            } else {
                $responseMessageContent = str_replace('"', '\"', $this->options['fakeLLMOutput']);
            }
        } else {
            $faker = Factory::create();
            $responseMessageContent = $faker->sentence(20);
        }
        $promptTokens = $this->messagesMeta['all']['totalTokens'] ?? 0;
        $completionTokens = $this->countTokens($responseMessageContent);
        $totalTokens = $promptTokens + $completionTokens;

        $responseID = "chatcmpl-" . mt_rand(100, 10000);
        $responseCreated = time();
        $jsonResponse = <<<EOT
{
"id": "{$responseID}",
"fakeLLM": true,
"object": "chat.completion",
"created": {$responseCreated},
"model": "{$ChatCompletionOptions['model']}",
"choices": [{
    "index": 0,
    "message": {
        "role": "assistant",
        "content": "{$responseMessageContent}",
        "functionCall": null
    },
    "finish_reason": "stop"
}],
"usage": {
    "promptTokens": {$promptTokens},
    "completionTokens": {$completionTokens},
    "totalTokens": {$totalTokens}
}
}
EOT;
        $this->usage['promptTokens'] += $promptTokens;
        $this->usage['completionTokens'] += $completionTokens;
        $this->usage['totalTokens'] += $totalTokens;
        return json_decode($jsonResponse);
    }

    /**
     * Send messages to the OpenAI API and retrieve the response.
     *
     * @param array $messages
     * @param array $ChatCompletionOptions
     * @param bool $noStreamClient
     * @return mixed
     */
    private function sendMessageToLLM($messages = [], $ChatCompletionOptions = [], $noStreamClient = false)
    {
        if(!$this->LLMReady) {
            $this->setupLLM();
        }

        if($ChatCompletionOptions) {
            $ChatCompletionOptions = array_merge($this->ChatCompletionOptions, $ChatCompletionOptions);
        } else {
            $ChatCompletionOptions = $this->ChatCompletionOptions;
        }
        if(!$messages) {
            $messages = $this->messages;
        }

        if(isset($this->options['fakeLLM']) && $this->options['fakeLLM']) {
            $response = $this->sendMessageToFakeLLM($ChatCompletionOptions);
        } else {
            if($this->options['stream'] && $noStreamClient == false) {
                $messages = [
                    ['role' => 'user', 'content' => 'Hello!'],
                ];
                $stream = $this->stream($messages);
                $stream = $this->LLMclient->chat()->createStreamed(
                    array_merge(
                        $ChatCompletionOptions,
                        ['messages' => $messages ]
                    )
                );
                $newMessage = [ 'role'=> 'assistant', 'content'=> ''];
                foreach($stream as $response){
                    if (connection_aborted()) {
                        break;
                    }
                    if(isset($response->choices[0])){
                        $newMessage['content'] .= $response->choices[0]->delta->content;
                        print(json_encode($response->choices[0]));
                    }
                }
                $response = ['stream' => $newMessage];
            }else{
                $response = $this->LLMclient->chat()->create(
                    array_merge(
                        $ChatCompletionOptions,
                        ['messages' => $messages ]
                    )
                );
            }
        }
        if(isset($response->usage->promptTokens)) $this->usage['promptTokens'] += $response->usage->promptTokens;
        if(isset($response->usage->completionTokens)) $this->usage['completionTokens'] += $response->usage->completionTokens;
        if(isset($response->usage->totalTokens)) $this->usage['totalTokens'] += $response->usage->totalTokens;

        $this->debug('sendMessageToLLM()', ['ChatCompletionOptions' => $ChatCompletionOptions, '$messages'=>$messages, '$response'=>$response]);
        return $response;
    }
}
