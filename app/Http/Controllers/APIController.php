<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Api;
use App\Models\App;
use Illuminate\Support\Facades\Cache;
use App\Jobs\UpdateAPIUsageJob;

class APIController extends Controller
{
    protected $ApiModel;
    protected $appId;
    private $options = [];

    public function execute(string $appId, string $apiId, Request $request) : JsonResponse{
        $this->appId = $appId;
        $authToken = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $authToken);
        if($token == '') return $this->responseMessage(['message'=>'No Token'], 403);

        $app = Cache::rememberForever('app:'.$appId, function () use($appId, $token) {
            return App::where(['id'=>$appId])->first();
        });
        if(!$app){
            return response()->json(['message' => 'Invalid app', 'status' => false], 403);
        }
        if($app->authToken != $token){
            return response()->json(['message' => 'Invalid token', 'status' => false], 403);
        }
        if(!$app->isActive){
            return response()->json(['message' => 'App is not active', 'status' => false], 403);
        }

        $this->ApiModel = Cache::rememberForever('appId:'.$app->id.'apiModel:'.$apiId, function () use($app, $apiId) {
            return $app->apis()->wherePivot('api_id', $apiId)->first();
        });

        if (is_null($this->ApiModel) || $this->ApiModel->isActive == false) {
            return $this->responseMessage(['message'=>'Access Denied'], 403);
        }
        
        $className = '\App\Services\\' . Cache::rememberForever('service:class:'.$this->ApiModel->service_id, function () {
            return $this->ApiModel->service->className;
        });

        $APIservice = new $className($request->all(), $this->ApiModel, $request, $app);
        $response = $APIservice->execute();

        if($this->ApiModel->enableUsage){
            $response['usage']['app_id'] = $appId;
            $response['usage']['api_id'] = $apiId;
            UpdateAPIUsageJob::dispatch($response['usage']);      
        }
        unset($response['usage']);
        return $this->responseMessage($response);
    }

    protected function responseMessage($response = [], $http_status = 200){
        $defaultResponse = [
            'status' => false,
            'message' => null,
            'serviceResponse' => null,
        ];
        $response = array_merge($defaultResponse, $response);
        if(!isset($http_status)) $http_status = ($response['status'] ? 200 : 400);
        return  response()->json($response, $http_status);
    }


}