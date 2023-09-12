<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\APIEndPoint;

class AIServiceController extends Controller
{
    private $APIEndPoint;
    private $apiOptions = [];
    private $NotOptions = [];
    private $options = [];

    public function service(Request $request, string $id) : JsonResponse
    {
        $this->APIEndPoint = APIEndPoint::where(['id'=>$id, 'isActive'=>true])->first();
        if(!$this->APIEndPoint) return $this->errorMessage('No API found');
        $this->prepareOptions();
        $this->options = array_merge(
            $this->apiOptions,
            $request->all(),
            $this->NotOptions,
        );
        $className = '\App\AIEndPoints\\' . $this->APIEndPoint->aiendpoint->className;
        $APIservice = new $className($this->options, $this->APIEndPoint);
        return response()->json($this->options);
        return response()->json($APIservice->execute());
        return response()->json($this->options);
    }

    private function prepareOptions(){
        foreach($this->APIEndPoint->requestSchema as $index => $item){
            if(isset($item['isApiOption']) && $item['isApiOption']){
                $this->apiOptions[$item['name']] = $item['default'] ?? null;
            }elseif(( !isset($item['isApiOption']) || $item['isApiOption'] == false )){
                $this->NotOptions[$item['name']] = $item['default'] ?? null;
            }
        }
    }
    private function errorMessage($message)
    {
        return response()->json(array_merge($this->responseMessageBase(status: 'error'), [
            'message' => $message,
        ]));
    }

    private function responseMessageBase(?string $status = 'unknown'){
        return [
            'status' => $status,
        ];
    }
}