<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Api;

class APIController extends Controller
{
    protected $ApiModel;
    private $options = [];
    public function execute(Request $request, string $id) : JsonResponse
    {
        $this->ApiModel = Api::where(['id'=>$id, 'isActive'=>true])->first();
        if(!$this->ApiModel) return $this->responseMessage(['message'=>'No API found']);
        $className = '\App\Services\\' . $this->ApiModel->service->className;
        $APIservice = new $className($request->all(), $this->ApiModel);
        $response = $APIservice->execute();
        
        $header_get_messages = $request->header('X-GET-MESSAGES', 0);
        if($header_get_messages){
            $response['__messages'] = $APIservice->getMessages();
        }
        return $this->responseMessage($response);
        // return  response()->json($response, ($response['status'] ? 200 : 400));
        
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