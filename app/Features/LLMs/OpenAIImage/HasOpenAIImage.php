<?php
namespace App\Features\LLMs\OpenAIImage;

use OpenAI;
use Faker\Factory;

trait HasOpenAIImage
{
    /**
     * An instance of the OpenAI API client.
     *
     * @var Client
     */
    protected $OpenAIImageLLMclient;

    /**
     * A boolean that indicates whether the OpenAI API client is ready to use.
     *
     * @var bool
     */
    protected $OpenAIImageLLMReady = false;

    /**
     * The options for the OpenAI completion.
     *
     * @var array
     */

    protected $OpenAIImageOptions;
    
    protected static $openAIImageCommonOptions = [
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
        'response_format' => [
            "name" => "response_format",
            "type" => "url",
            "required" => false,
            "desc" => "The format in which the generated images are returned. Must be one of url or b64_json.",
            "default" => 'url',
            "isApiOption" => false,
            "_group" => 'OpenAI',
        ],
        'n' => [
            "name" => "n",
            "type" => "integer",
            "required" => false,
            "desc" => "The number of images to generate. Must be between 1 and 10.",
            "default" => 1,
            "isApiOption" => false,
            "_group" => 'OpenAI',
        ],
        'size' => [
            "name" => "size",
            "type" => "string",
            "required" => false,
            "desc" => "The size of the generated images. Must be one of 256x256, 512x512, or 1024x1024.",
            "default" => '1024x1024',
            "isApiOption" => false,
            "_group" => 'OpenAI',
        ],
        'user' => [
            "name" => "user",
            "type" => "string",
            "required" => false,
            "desc" => "A unique identifier representing your end-user, which can help OpenAI to monitor and detect abuse.",
            "default" => 'url',
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
    ];
    protected static $openAICreateImageOptions = [
        'prompt' => [
            "name" => "prompt",
            "type" => "string",
            "required" => true,
            "desc" => "A text description of the desired image(s). The maximum length is 1000 characters.",
            "default" => null,
            "isApiOption" => true,
            "_group" => 'OpenAI',
        ],
    ];
    protected static $openAIEditImageOptions = [
        'prompt' => [
            "name" => "prompt",
            "type" => "string",
            "required" => true,
            "desc" => "A text description of the desired image(s). The maximum length is 1000 characters.",
            "default" => null,
            "isApiOption" => true,
            "_group" => 'OpenAI',
        ],
        'image' => [
            "name" => "image",
            "type" => "file",
            "required" => true,
            "desc" => "The image to edit. Must be a valid PNG file, less than 4MB, and square. If mask is not provided, image must have transparency, which will be used as the mask.",
            "default" => null,
            "isApiOption" => true,
            "_group" => 'OpenAI',
        ],
        'imageURL' => [
            "name" => "imageURL",
            "type" => "file",
            "required" => true,
            "desc" => "The image URL to edit. Must be a valid PNG file, less than 4MB, and square. If mask is not provided, image must have transparency, which will be used as the mask. (you can use this or image option)",
            "default" => null,
            "isApiOption" => true,
            "_group" => 'OpenAI',
        ],
        'mask' => [
            "name" => "mask",
            "type" => "file",
            "required" => false,
            "desc" => "An additional image whose fully transparent areas (e.g. where alpha is zero) indicate where image should be edited. Must be a valid PNG file, less than 4MB, and have the same dimensions as image.",
            "default" => null,
            "isApiOption" => true,
            "_group" => 'OpenAI',
        ],
        'maskURL' => [
            "name" => "maskURL",
            "type" => "file",
            "required" => false,
            "desc" => "An additional image URL whose fully transparent areas (e.g. where alpha is zero) indicate where image should be edited. Must be a valid PNG file, less than 4MB, and have the same dimensions as image. (you can use this or mask option)",
            "default" => null,
            "isApiOption" => true,
            "_group" => 'OpenAI',
        ],
    ];
    protected static $openAIVariationsImageOptions = [
        'image' => [
            "name" => "image",
            "type" => "file",
            "required" => true,
            "desc" => "The image to use as the basis for the variation(s). Must be a valid PNG file, less than 4MB, and square.",
            "default" => null,
            "isApiOption" => true,
            "_group" => 'OpenAI',
        ],
        'imageURL' => [
            "name" => "imageURL",
            "type" => "file",
            "required" => true,
            "desc" => "The image to use as the basis for the variation(s). Must be a valid PNG file, less than 4MB, and square. (you can use this or image option)",
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
    private function setupOpenAIImageLLM()
    {
        $this->LLMReady = true;
        $this->OpenAIImageOptions = [];
        if(!isset($this->options['openaiApiKey'])) {
            $this->options['openaiApiKey'] = config('openai.api_key');
        }
        foreach(['n','prompt','size','response_format','user'] as $key) {
            if(isset($this->options[$key])) {
                $this->OpenAIImageOptions[$key] = $this->options[$key];
            }
        }

        $this->OpenAIImageLLMclient = OpenAI::factory()->withApiKey($this->options['openaiApiKey'])->withBaseUri($this->options['openaiBaseUri']);

        if($this->options['openaiApiVersion']) {
            $this->OpenAIImageLLMclient = $this->OpenAIImageLLMclient->withQueryParam('api-version', $this->options['openaiApiVersion']);
        }
        if($this->options['openaiOrganization']) {
            $this->OpenAIImageLLMclient = $this->OpenAIImageLLMclient->withOrganization($this->options['openaiOrganization']);
        }
        
        $this->debug('setupLLM()', ['OpenAIImageOptions' => $this->OpenAIImageOptions]);

        $this->OpenAIImageLLMclient = $this->OpenAIImageLLMclient->make();
    }

    private function getRandomImage($size='256x256', $response_format='url', $keyword = null)
    {
        $imageUrl = 'https://source.unsplash.com/random/' . $size . '/?' . $keyword;
        $image = [
            'url' => null,
            'b64_json' => null,
        ];

        if($response_format == 'b64_json'){
            $image['b64_json'] = base64_encode(file_get_contents($imageUrl));
        }else{
            $image['url'] = $imageUrl;
        }
        return $image;
    }


    
    /**
     * Generate a fake response from the OpenAI API.
     *
     * @param array $OpenAIImageOptions
     * @return mixed
     */
    private function sendImageRequestToFakeLLM($OpenAIImageOptions = [], $service = 'create')
    {
        $jsonResponse = [ 
            'created' => time(),
            'data' => [],
        ];

        if(!isset($this->options['n']) || !$this->options['n']) {
            $n = 1;
        }else{
            $n = $this->options['n'];
        }

        for($i = 0; $i<$n; $i++){
            $jsonResponse['data'][] = $this->getRandomImage($this->options['size'], $this->options['response_format']);
        }

        $jsonResponse = json_encode($jsonResponse);
        
        return json_decode($jsonResponse);
    }

    /**
     * Send request to the OpenAI API and retrieve the response.
     *
     */
    private function sendImageRequestToLLM($service = 'create', $OpenAIImageOptions = [])
    {
        if(!$this->OpenAIImageLLMReady) {
            $this->setupOpenAIImageLLM();
        }

        if($OpenAIImageOptions) {
            $OpenAIImageOptions = array_merge($this->OpenAIImageOptions, $OpenAIImageOptions);
        } else {
            $OpenAIImageOptions = $this->OpenAIImageOptions;
        }
        if(isset($this->options['fakeLLM']) && $this->options['fakeLLM']) {
            $response = $this->sendImageRequestToFakeLLM($OpenAIImageOptions, $service);
        } else {
            if($service == 'variation'){
                $OpenAIImageOptions['image'] = fopen((string) $this->options['image'], 'r');
                $response = $this->OpenAIImageLLMclient->images()->variation($OpenAIImageOptions);
                unset($OpenAIAudioOptions['image']);
            }elseif($service == 'edit'){
                $OpenAIImageOptions['image'] = fopen((string) $this->options['image'], 'r');
                if(isset($this->options['mask']) && $this->options['mask']) $OpenAIImageOptions['mask'] = fopen((string) $this->options['mask'], 'r');
                $response = $this->OpenAIImageLLMclient->images()->edit($OpenAIImageOptions);
                unset($OpenAIAudioOptions['image']);
                unset($OpenAIAudioOptions['mask']);
            }else{
                $response = $this->OpenAIImageLLMclient->images()->create($OpenAIImageOptions);
            }
        }
        $this->debug('sendImageRequestToLLM()', ['OpenAIImageOptions' => $OpenAIImageOptions,'$response'=>$response]);
        return $response;
    }
}
