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
        $testAPIOptionsJson = json_encode($testAPIOptions, JSON_UNESCAPED_UNICODE);
        return view('api-docs.api', compact('app', 'apis', 'api', 'testAPIOptions','testAPIOptionsJson'));
    }

    public function collectionCreate(Request $request){
        return view('api-docs.collection_document_create');
    }
    public function collectionUpdate(Request $request){
        return view('api-docs.collection_document_update');
    }
    public function collectionDelete(Request $request){
        return view('api-docs.collection_document_delete');
    }
    public function collectionGet(Request $request){
        return view('api-docs.collection_document_get');
    }
    public function collectionList(Request $request){
        return view('api-docs.collection_documents_list');
    }
    public function collectionStatus(Request $request){
        return view('api-docs.collection_document_status');
    }
}