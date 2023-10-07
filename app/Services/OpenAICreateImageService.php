<?php

namespace App\Services;

use App\Features\HasCaching;
use App\Features\LLMs\OpenAIImage\HasOpenAIImage;
use App\Features\HasDebug;

class OpenAICreateImageService extends BaseService
{
    use HasCaching;
    use HasOpenAIImage;
    use HasDebug;

    protected $features = [
        'cachingOptions',
        'openAICreateImageOptions',
        'openAIImageCommonOptions',
        'debugOptions'
    ];

    protected $ApiModel;
    protected $api_id;
    protected $app_id;
    protected $httpRequest;


    public function __construct($userOptions = [], $ApiModel = null, $httpRequest = null, $app = null){
        if($userOptions) {
            $this->ApiModel = $ApiModel;
            $this->api_id = $ApiModel->id;
            $this->app_id = $app->id ?? 0;
            $debug = [
                'requestOptions' => $userOptions
            ];
            $this->httpRequest = $httpRequest;
            if($httpRequest) $debug['header'] = $httpRequest->header();
            $this->initOptions($userOptions);

            $this->debug('input', $debug);
        }
    }

    public function execute(){
        try {
            $clearCache = false;

            $this->__cacheInit([
                'openaiBaseUri',
                'openaiApiVersion',
                'openaiOrganization',
                'prompt',
                'n',
                'size',
                'user',
                'response_format',
            ]);

            if($this->options['clearCache']) $clearCache = $this->clearCache();

            if(!$this->options['prompt']) {
                if($clearCache) {
                    return $this->responseMessage(['status' => true, 'message' => 'Cache cleared']);
                }
                return $this->responseMessage(['message' => 'No prompt defined.']);
            }

            $serviceResponse = $this->getCache();
            if($serviceResponse) {
                return $this->responseMessage(['status' => true, 'serviceResponse' => $serviceResponse, 'cached'=>true, 'cacheScope' => $this->options['cacheScope']]);
            }

            $serviceResponse = $this->sendImageRequestToLLM('create');
            $this->setCache($serviceResponse);

            $response = [
                'status' => true, 
                'serviceResponse' => $serviceResponse,
                'serviceMeta' => $this->getMeta($serviceResponse),
            ];

        } catch (\Throwable $th) {
            $response = [
                'status' => false, 
                'message' => $th->getMessage(), 
            ];
        }

        return $this->responseMessage($response);
    }

}
