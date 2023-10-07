<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\App;
use App\Models\Api;

class APIDocsController extends Controller
{
    public function viewApp(Request $request, $appDocToken, $apiID = null){
        $app = App::where('docToken', $appDocToken)->first();
        if(!$app) abort(404);
        $apis = $app->apis()->get();
        if(!$apiID){
            $api = $app->apis()->first();
        }else{
            $api = $app->apis()->wherePivot('id', $apiID)->first();
        }
        $testAPIOptions = [];
        foreach($api->options as $group => $groupOptions){
            foreach($groupOptions as $option){
                if(!$option['isApiOption']) continue;
                // if option name start with clear* then skip
                if(preg_match('/^clear/', $option['name'])) continue;
                // if option name is debug then skip
                if($option['name'] == 'debug') continue;
                // if option start with return then skip
                if(preg_match('/^return/', $option['name'])) continue;
                // if option start with update then skip
                if(preg_match('/^update/', $option['name'])) continue;
                // if option has Token then skip
                if(preg_match('/Token/', $option['name'])) continue;
                if($option['name'] == 'userMessage'){
                    $option['default'] = 'How are you doing?';
                }
                $testAPIOptions[$option['name']] = $option['default'];
            }
        }
        $viewType = 'viewApp';
        $testAPIOptionsJson = json_encode($testAPIOptions, JSON_UNESCAPED_UNICODE);
        return view('api-docs.api', compact('app', 'apis', 'api', 'testAPIOptions','testAPIOptionsJson','viewType'));
    }
    public function viewAppCollection(Request $request, $appDocToken, $contentView = 'document_create'){
        $app = App::where('docToken', $appDocToken)->first();
        $apis = $app->apis()->get();
        $viewType = 'AppCollection';
        if(!$app) abort(404);
        if(!in_array($contentView, ['collection_create','collection_delete','document_create','document_update','document_delete','document_get','documents_list','document_status'])) abort(404);
        return view('api-docs.api', compact('app', 'apis','viewType','contentView'));
    }

    public function collectionCreate(Request $request){
        return view('api-docs.collection.documents', ['title'=>'Create Document','contentView'=>'document_create']);
    }
    public function collectionUpdate(Request $request){
        return view('api-docs.collection.documents', ['title'=>'Update Document','contentView'=>'document_update']);
    }
    public function collectionDelete(Request $request){
        return view('api-docs.collection.documents', ['title'=>'Delete Document','contentView'=>'document_delete']);
    }
    public function collectionGet(Request $request){
        return view('api-docs.collection.documents', ['title'=>'Get Document','contentView'=>'document_get']);
    }
    public function collectionList(Request $request){
        return view('api-docs.collection.documents', ['title'=>'List Documents','contentView'=>'documents_list']);
    }
    public function collectionStatus(Request $request){
        return view('api-docs.collection.documents', ['title'=>'Document Status','contentView'=>'document_status']);
    }
}