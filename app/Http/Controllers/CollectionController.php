<?php

namespace App\Http\Controllers;

use App\Jobs\DocumentLoaderJob;
use App\Models\Collection;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\App;

class CollectionController extends Controller
{
    public function createAppCollection(Request $request){
        $validator = Validator::make($request->all(), [
            'app_id' => 'required|numeric',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:255',
            'context_prompt' => 'nullable|string|max:255',
            'defaultTotalReturnDocuments' => 'nullable|numeric',
            'loader_id' => 'nullable|numeric',
            'splitter_id' => 'nullable|numeric',
            'embedder_id' => 'nullable|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors(), 'status' => false], 404);
        }
        $app_id = $request->app_id;
        $authToken = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $authToken);
        if($token == '') return $this->responseMessage(['message'=>'No Token'], 403);
        $app = Cache::rememberForever('app:'.$app_id, function () use($app_id, $token) {
            return App::where(['id'=>$app_id])->first();
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
        if(!$app->enableAppCollection){
            return response()->json(['message' => 'Invalid request', 'status' => false], 403);
        }
        $data = $request->all();
        $data['authToken'] = bin2hex(openssl_random_pseudo_bytes(16)). bin2hex(random_bytes(16));
        $data['app_id'] = $app_id;
        $data['collection_type'] = 'app';
        if(!isset($data['context_prompt'])) $data['context_prompt'] = "Answer the following Question based on the Context only. Only answer from the Context. Try to provide a reference. If you don't know the answer mention that you couldn't find the answer.\nCONTEXT: {{context}}\n\nnQuestion:{{userMessage}}";
        if(!isset($data['defaultTotalReturnDocuments'])) $data['defaultTotalReturnDocuments'] = 3;
        if(!isset($data['loader_id'])) $data['loader_id'] = 1;
        if(!isset($data['splitter_id'])) $data['splitter_id'] = 1;
        if(!isset($data['embedder_id'])) $data['embedder_id'] = 1;
        $collection = Collection::create($data);
        return response()->json(['collection_id' => $collection->id, 'authToken' => $collection->authToken, 'status' => true], 200);
    }
    public function deleteAppCollection(Request $request){
        $validator = Validator::make($request->all(), [
            'app_id' => 'required|numeric',
            'collection_id' => 'required|numeric',
            'collection_authToken' => 'required|string|max:200',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors(), 'status' => false], 404);
        }
        $app_id = $request->app_id;
        $collection_id = $request->collection_id;
        $collection_authToken = $request->collection_authToken;


        $authToken = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $authToken);
        if($token == '') return $this->responseMessage(['message'=>'No Token'], 403);
        $app = Cache::rememberForever('app:'.$app_id, function () use($app_id, $token) {
            return App::where(['id'=>$app_id])->first();
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
        if(!$app->enableAppCollection){
            return response()->json(['message' => 'Invalid request', 'status' => false], 403);
        }

        $collection = Cache::rememberForever('collection:'.$collection_id, function () use($collection_id) {
            return Collection::where(['id'=>$collection_id])->first();
        });
        if(!$collection || $collection->authToken != $collection_authToken || $collection->app_id != $app_id){
            return response()->json(['message' => 'Invalid collection', 'status' => false], 403);
        }

        $collection->delete();
        return response()->json(['message' => 'Collection deleted', 'status' => true], 200);
    }
    
}
