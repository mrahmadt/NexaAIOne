<?php

namespace App\Services;

use App\Features\HasCaching;
use App\Features\LLMs\OpenAIImage\HasOpenAIImage;
use App\Features\HasDebug;
use Illuminate\Support\Facades\Http;

class OpenAIEditImageService extends BaseService
{
    use HasCaching;
    use HasOpenAIImage;
    use HasDebug;

    protected $features = [
        'cachingOptions',
        'openAIEditImageOptions',
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

            if ($this->httpRequest && $this->httpRequest->file('image')->isValid()) {
                $this->options['image'] = $this->httpRequest->file->path() . '.' . $this->httpRequest->file('image')->getClientOriginalName();
            }elseif(isset($this->options['imageURL'])){
                $tempFile = tempnam(sys_get_temp_dir(), 'imageFile_');
                $response = Http::sink($tempFile)->get($this->options['imageURL']);
                $response->throw();
                $this->options['image'] = $tempFile;
            }

            if ($this->httpRequest && $this->httpRequest->file('mask')->isValid()) {
                $this->options['mask'] = $this->httpRequest->file->path() . '.' . $this->httpRequest->file('mask')->getClientOriginalName();
            }elseif(isset($this->options['maskURL'])){
                $tempFile = tempnam(sys_get_temp_dir(), 'imageFile_');
                $response = Http::sink($tempFile)->get($this->options['maskURL']);
                $response->throw();
                $this->options['mask'] = $tempFile;
            }

            $md5file = md5_file($this->options['image']);
            if (isset($this->options['mask'])) {
                $md5file .=  md5_file($this->options['mask']);
            }

            $this->__cacheInit([
                'openaiBaseUri',
                'openaiApiVersion',
                'openaiOrganization',
                'prompt',
                'n',
                'size',
                'user',
                'response_format',
            ], $md5file);

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

            $serviceResponse = $this->sendImageRequestToLLM('edit');
            
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
