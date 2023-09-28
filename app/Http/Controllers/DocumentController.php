<?php

namespace App\Http\Controllers;

use App\Jobs\DocumentLoaderJob;
use App\Models\Collection;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    public function documentStatus($jobID, Request $request)
    {
        try {
            // Fetch the status of the job from the cache
            $jobStatus = Cache::get($jobID);
            if (!$jobStatus) {
                // Job ID not found in cache. This means the document was either created or the jobID is invalid
                return response()->json(['message' => 'The document has been created or the job ID is invalid', 'status' => true], 200);
            }
            return response()->json(['jobStatus' => $jobStatus, 'status' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage(), 'status' => false], 500);
        }
    }

    
    public function getDocument($document_id, Request $request)
    {
        try {
            // Fetch the Document by ID
            $document = Document::find($document_id);
            if (!$document) {
                return response()->json(['message' => 'Document not found', 'status' => false], 404);
            }
            // Check Authorization token
            $collection = $this->checkAuthToken($document->collection_id, $request);
            if (!$collection) {
                return response()->json(['message' => 'Invalid token', 'status' => false], 403);
            }
            // Check if the Document belongs to the Collection
            if ($document->collection_id !== $collection->id) {
                return response()->json(['message' => 'Document does not belong to this collection', 'status' => false], 403);
            }
            return response()->json([
                'document_id' => $document->id,
                'content' => $document->content,
                'content_tokens' => $document->content_tokens,
                'meta' => $document->meta,
                // 'embeds' => $document->embeds,  // it's hidden!
                'created_at' => $document->created_at,
                'updated_at' => $document->updated_at,
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage(), 'status' => false], 500);
        }
    }


    public function listDocuments($collection_id, Request $request)
    {
        // Validation for incoming request
        $validator = Validator::make($request->all(), [
            'per_page' => 'nullable|numeric|min:1|max:50',
            'page' => 'nullable|numeric|min:1'
        ]);
    
        // Check Authorization token
        $collection = $this->checkAuthToken($collection_id, $request);
        if (!$collection) {
            return response()->json(['message' => 'Invalid token or collection ID.', 'status' => false], 403);
        }
    
        try {
            // Number of documents per page, with a default value
            $perPage = $request->input('per_page', 10);

            // Fetch documents and apply pagination
            $documents = Document::where('collection_id', $collection_id)
                ->select('id', 'content', 'meta', 'created_at', 'updated_at')
                ->orderBy('id', 'asc')
                ->paginate($perPage);
            
            // Add pagination info to the response
            $response = [
                'documents' => $documents->items(),
                'pagination' => [
                    'total' => $documents->total(),
                    'per_page' => $documents->perPage(),
                    'current_page' => $documents->currentPage(),
                    'last_page' => $documents->lastPage(),
                    'next_page_url' => $documents->nextPageUrl(),
                    'prev_page_url' => $documents->previousPageUrl()
                ],
                'status' => true
            ];
    
            return response()->json($response, 200);
    
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage(), 'status' => false], 500);
        }
    }

    private function checkAuthToken($collection_id, Request $request){
        $authToken = $request->header('Authorization');
        // Extract token after the word 'Bearer'
        $token = str_replace('Bearer ', '', $authToken);
        // Fetch the Collection associated with the authToken
        if(!isset($token)) return false;
        $collection = Collection::where(['id'=>$collection_id, 'authToken'=> $token])->first();
        if($collection){
            return $collection;
        }else{
            return false;
        }
    }

    public function delete($document_id, Request $request) {
        try {
            // Fetch the Document by ID
            $document = Document::find($document_id);
    
            if (!$document) {
                return response()->json(['message' => 'Document not found', 'status' => false], 404);
            }

            $collection = $this->checkAuthToken($document->collection_id, $request);
            if (!$collection) {
                return response()->json(['message' => 'Invalid token', 'status' => false], 403);
            }

            // Check if the Document belongs to the Collection
            if ($document->collection_id !== $collection->id) {
                return response()->json(['message' => 'Document does not belong to this collection', 'status' => false], 403);
            }
    
            // Delete the Document
            $document->delete();
    
            return response()->json(['message' => 'Document deleted successfully', 'status' => true], 200);
    
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage(), 'status' => false], 500);
        }
    }
    public function update($document_id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'collection_id' => 'required|numeric|exists:collections,id',
            'content' => 'required_without_all:url,file,meta',
            'url' => 'required_without_all:content,file,meta|url',
            'file' => 'required_without_all:content,url,meta|mimes:txt,md',
            'meta' => 'required_without_all:content,url,file|json',
            'splitter_id' => 'nullable|numeric',
            'loader_id' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors(), 'status' => false], 404);
        }
        return $this->createOrUpdate($request, $document_id);
    }
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'collection_id' => 'required|numeric|exists:collections,id',
            'content' => 'required_without_all:url,file',
            'url' => 'required_without_all:content,file|url',
            'file' => 'required_without_all:content,url|mimes:txt,md',
            'meta' => 'nullable|json',
            'splitter_id' => 'nullable|numeric',
            'loader_id' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors(), 'status' => false], 404);
        }
        return $this->createOrUpdate($request);
    }

    private function createOrUpdate(Request $request, $document_id = null)
    {
        $collection = $this->checkAuthToken($request->collection_id, $request);        
        if (!$collection) {
            return response()->json(['message' => 'Authorization header is missing.', 'status' => false], 403);
        }

        $jobID = (string) Str::uuid();

        $content = $request->content;
        $url = $request->url;
        $file = $request->file('file');

        $meta = $request->meta ?? null;

        if($request->splitter_id === 0){
            $splitter_id = false;
        }elseif($request->splitter_id){
            $splitter_id = $request->splitter_id; 
        }else{
            $splitter_id = $collection->splitter_id;
        }

        $loader_id = $collection->loader_id;
        if($request->loader_id){
            $loader_id = $request->loader_id; 
        }elseif(!isset($loader_id)){
            $loader_id = 1;
        }

        $data = [
            'jobID' => $jobID,
            'document_id' => $document_id, // or null
            'collection_id' => $request->collection_id,
            'content' => $content, //or null
            'url' => $url, //or null
            'file' => $file, //or null
            'meta' => $meta, // or null
            'splitter_id' => $splitter_id, // false, number or null
            'loader_id' => $loader_id, // number
        ];

        // Dispatch the job
        DocumentLoaderJob::dispatch($data);

        return response()->json(['jobID' => $jobID, 'status' => true]);
    }
}
