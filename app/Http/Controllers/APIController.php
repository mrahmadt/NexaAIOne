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

        exit;
        $this->ApiModel = Api::where(['id'=>$id, 'isActive'=>true])->first();
        if(!$this->ApiModel) return $this->errorMessage('No API found');

        $className = '\App\Services\\' . $this->ApiModel->service->className;
        $APIservice = new $className($this->options, $this->ApiModel);
        return response()->json($this->options);
        return response()->json($APIservice->execute());
        return response()->json($this->options);
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