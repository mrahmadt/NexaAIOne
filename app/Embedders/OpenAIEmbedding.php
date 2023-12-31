<?php
namespace App\Embedders;

use OpenAI;
use Faker\Factory;

class OpenAIEmbedding
{
    /**
     * An instance of the OpenAI API client.
     *
     * @var Client
     */
    protected $client;
    protected $options;

    public function __construct($options)
    {
        $this->options['apiKey'] = config('openai.api_key');
        $this->options['model'] = config('openai.default_embeddings_model');
        $this->options['fakeLLM'] = false;
        
        foreach(['apiKey','baseUri','apiVersion','organization','model','fakeLLM'] as $key){
            if(isset($options[$key])) $this->options[$key] = $options[$key];
        }
        
        $this->client = OpenAI::factory()->withApiKey($this->options['apiKey']);

        if(isset($this->options['baseUri'])) {
            $this->client = $this->client->withBaseUri($this->options['baseUri']);
        }

        if(isset($this->options['apiVersion'])) {
            $this->client = $this->client->withQueryParam('api-version', $this->options['apiVersion']);
        }

        if(isset($this->options['organization'])) {
            $this->client = $this->client->withOrganization($this->options['organization']);
        }

        $this->client = $this->client->make();
    }

    public function execute($input){
        if($this->options['fakeLLM']){
            return json_encode([
                    "object"=> "list",
                    "data"=> [
                      [
                        "object"=> "embedding",
                        "embedding"=> [
                          0.0023064255,
                          -0.009327292,
                          -0.0028842222,
                        ],
                        "index"=> 0
                      ]
                    ],
                    "model"=> "text-embedding-ada-002",
                    "usage"=> [
                      "prompt_tokens"=> 8,
                      "total_tokens"=> 8
                    ]
            ], JSON_FORCE_OBJECT);
        }
        $response = $this->client->embeddings()->create([
            'model' => $this->options['model'],
            'input' => $input,
        ]);

        if(!isset($response->embeddings[0])){
            return false;
        }else{
            return $response;
        }
    }
}