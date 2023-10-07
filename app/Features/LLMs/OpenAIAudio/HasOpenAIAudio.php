<?php
namespace App\Features\LLMs\OpenAIAudio;

use OpenAI;
use Faker\Factory;

trait HasOpenAIAudio
{
    /**
     * An instance of the OpenAI API client.
     *
     * @var Client
     */
    protected $OpenAIAudioLLMclient;

    /**
     * A boolean that indicates whether the OpenAI API client is ready to use.
     *
     * @var bool
     */
    protected $OpenAIAudioLLMReady = false;

    /**
     * The options for the OpenAI completion.
     *
     * @var array
     */

    protected $OpenAIAudioOptions;
    protected static $openAIAudioCommonOptions = [
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
        'file' => [
            "name" => "file",
            "type" => "file",
            "required" => true,
            "desc" => "The audio file in one of these formats: flac, mp3, mp4, mpeg, mpga, m4a, ogg, wav, or webm.",
            "default" => null,
            "isApiOption" => true,
            "_group" => 'OpenAI',
        ],
        'fileURL' => [
            "name" => "file",
            "type" => "file url",
            "required" => true,
            "desc" => "The audio file URL in one of these formats: flac, mp3, mp4, mpeg, mpga, m4a, ogg, wav, or webm. (you can use this or file option)",
            "default" => null,
            "isApiOption" => true,
            "_group" => 'OpenAI',
        ],
        'model' => [
            "name" => "model",
            "type" => "select",
            "required" => false,
            "desc" => "The LLM model to use.",
            "default" => "whisper-1",
            "isApiOption" => false,
            "_group" => 'OpenAI',
        ],
        'response_format' => [
            "name" => "response_format",
            "type" => "string",
            "required" => false,
            "desc" => "The format of the transcript output, in one of these options: json, srt, verbose_json, or vtt.",
            "default" => 'verbose_json',
            "isApiOption" => true,
            "_group" => 'OpenAI',
        ],
        'temperature' => [
            "name" => "temperature",
            "type" => "number",
            "required" => false,
            "desc" => "The sampling temperature, between 0 and 1. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic. If set to 0, the model will use log probability to automatically increase the temperature until certain thresholds are hit.",
            "default" => 1,
            "isApiOption" => true,
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
            "type" => "string / OpenAI Audio Response in JSON",
            "required" => false,
            "desc" => "You can define the fake LLM response or it will return random string.",
            "default" => null,
            "isApiOption" => true,
            "_group" => 'OpenAI',
        ],
    ];
    protected static $openAITranscriptionOptions = [
        'prompt' => [
            "name" => "prompt",
            "type" => "string",
            "required" => false,
            "desc" => "An optional text to guide the model's style or continue a previous audio segment. The prompt should match the audio language.",
            "default" => null,
            "isApiOption" => true,
            "_group" => 'OpenAI',
        ],
        'language' => [
            "name" => "language",
            "type" => "string",
            "required" => false,
            "desc" => "The language of the input audio. Supplying the input language in ISO-639-1 format will improve accuracy and latency.",
            "default" => null,
            "isApiOption" => true,
            "_group" => 'OpenAI',
        ],
    ];
    protected static $openAITranslationOptions = [
        'prompt' => [
            "name" => "prompt",
            "type" => "string",
            "required" => false,
            "desc" => "An optional text to guide the model's style or continue a previous audio segment. The prompt should be in English.",
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
    private function setupOpenAIAudioLLM()
    {
        $this->LLMReady = true;
        $this->OpenAIAudioOptions = [];
        if(!isset($this->options['openaiApiKey'])) {
            $this->options['openaiApiKey'] = config('openai.api_key');
        }
        foreach(['model','temperature','prompt','response_format','language'] as $key) {
            if(isset($this->options[$key])) {
                $this->OpenAIAudioOptions[$key] = $this->options[$key];
            }
        }

        $this->OpenAIAudioLLMclient = OpenAI::factory()->withApiKey($this->options['openaiApiKey'])->withBaseUri($this->options['openaiBaseUri']);

        if($this->options['openaiApiVersion']) {
            $this->OpenAIAudioLLMclient = $this->OpenAIAudioLLMclient->withQueryParam('api-version', $this->options['openaiApiVersion']);
        }
        if($this->options['openaiOrganization']) {
            $this->OpenAIAudioLLMclient = $this->OpenAIAudioLLMclient->withOrganization($this->options['openaiOrganization']);
        }
        
        $this->debug('setupLLM()', ['OpenAIAudioOptions' => $this->OpenAIAudioOptions]);

        $this->OpenAIAudioLLMclient = $this->OpenAIAudioLLMclient->make();
    }

    /**
     * Generate a fake response from the OpenAI API.
     *
     * @param array $OpenAIAudioOptions
     * @return mixed
     */
    private function sendAudioToFakeLLM($OpenAIAudioOptions = [], $service = 'transcribe')
    {
        $responseMessageContent = null;
        if(isset($this->options['fakeLLMOutput']) && $this->options['fakeLLMOutput']) {
            if(json_decode($this->options['fakeLLMOutput'])) {
                $jsonResponse = $this->options['fakeLLMOutput'];
                return json_decode($jsonResponse);
            } else {
                $responseMessageContent = $this->options['fakeLLMOutput'];
            }
        } else {
            $faker = Factory::create();
            $responseMessageContent = $faker->sentence(20);
        }
        $jsonResponse = [ 
            'text' => $responseMessageContent,
            'task' => null,
            'language' => null,
            'segments' => [],
        ];
        if($this->options['response_format']=='verbose_json'){
            $jsonResponse['task'] = $service;
            $jsonResponse['language'] = 'english';
            $jsonResponse['duration'] = 6.11;
        }elseif($this->options['response_format']=='srt'){
            $jsonResponse['text'] = "1\n00:00:00,000 --> 00:00:05,920\n" . $responseMessageContent . "\n\n\n";
        }elseif($this->options['response_format']=='vtt'){
            $jsonResponse['text'] = "WEBVTT\n\n00:00:00.000 --> 00:00:05.920\n" . $responseMessageContent . "\n\n";
        }
        $jsonResponse = [ 'text' => $responseMessageContent ];
        $jsonResponse = json_encode($jsonResponse);
        return json_decode($jsonResponse);
    }

    /**
     * Send request to the OpenAI API and retrieve the response.
     *
     */
    private function sendAudioToLLM(string $audioLocalFile, $service = 'transcribe', $OpenAIAudioOptions = [])
    {
        if(!$this->OpenAIAudioLLMReady) {
            $this->setupOpenAIAudioLLM();
        }

        if($OpenAIAudioOptions) {
            $OpenAIAudioOptions = array_merge($this->OpenAIAudioOptions, $OpenAIAudioOptions);
        } else {
            $OpenAIAudioOptions = $this->OpenAIAudioOptions;
        }
        if(isset($this->options['fakeLLM']) && $this->options['fakeLLM']) {
            $response = $this->sendAudioToFakeLLM($OpenAIAudioOptions, $service);
        } else {
            $OpenAIAudioOptions['file'] = fopen((string) $audioLocalFile, 'r');
            if($service == 'transcribe'){
                $response = $this->OpenAIAudioLLMclient->audio()->transcribe($OpenAIAudioOptions);
            }else{
                $response = $this->OpenAIAudioLLMclient->audio()->translate($OpenAIAudioOptions);
            }
            unset($OpenAIAudioOptions['file']);
        }
        
        $this->debug('sendAudioToLLM()', ['OpenAIAudioOptions' => $OpenAIAudioOptions, '$audioFile'=>$audioLocalFile, '$response'=>$response]);
        return $response;
    }
}

