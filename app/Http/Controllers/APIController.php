<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Api;
use App\Models\App;
use Illuminate\Support\Facades\Cache;
use App\Jobs\UpdateAPIUsage;

class APIController extends Controller
{
    protected $ApiModel;
    private $options = [];

    // private function checkAuthToken($appId, Request $request){
    //     $authToken = $request->header('Authorization');
    //     $token = str_replace('Bearer ', '', $authToken);
    //     $app = App::where(['id'=>$appId, 'authToken'=> $token])->first();
    //     if($app){
    //         return $app;
    //     }else{
    //         return false;
    //     }
    // }

    public function execute(string $appId, string $apiId, Request $request) : JsonResponse{
        $authToken = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $authToken);
        if(!isset($token)) return $this->responseMessage(['message'=>'Invalid token'],403);

        $app = Cache::rememberForever('appId:'.$appId, function () use($appId, $token) {
            return App::where(['id'=>$appId, 'authToken'=> $token])->select(['id'])->first();
        });

        // $app = App::where(['id'=>$appId, 'authToken'=> $token])->select(['id'])->first();
        if($app){
            return $app;
        }else{
            return $this->responseMessage(['message'=>'Invalid token'],403);
        }

        $this->ApiModel = Cache::rememberForever('apiModel:'.$apiId, function () use($app, $apiId) {
            return $app->apis()->wherePivot(['id'=>$apiId, 'isActive'=>true])->first();
        });
        // $this->ApiModel = $app->apis()->wherePivot(['id'=>$apiId, 'isActive'=>true])->first();
        if (!$this->ApiModel) {
            return $this->responseMessage(['message'=>'Access Denied'],403);
        }
        // $this->ApiModel = Api::where(['id'=>$apiId, 'isActive'=>true])->first();
        // if(!$this->ApiModel) return $this->responseMessage(['message'=>'No API found'],404);

        $className = '\App\Services\\' . Cache::rememberForever('service:class:'.$this->ApiModel->service_id, function () {
            return $this->ApiModel->service->className;
        });
        // $className = '\App\Services\\' . $this->ApiModel->service->className;

        $APIservice = new $className($request->all(), $this->ApiModel, $request);
        $response = $APIservice->execute();

        if($this->ApiModel->enableUsage){
            $response['usage']['app_id'] = $appId;
            UpdateAPIUsage::dispatch($response['usage']);      
        }
 
        unset($response['usage']);
        return $this->responseMessage($response);
    }

    protected function responseMessage($response = []){
        $defaultResponse = [
            'status' => false,
            'message' => null,
            'serviceResponse' => null,
        ];
        $response = array_merge($defaultResponse, $response);
        
        return  response()->json($response, ($response['status'] ? 200 : 400));
    }


}