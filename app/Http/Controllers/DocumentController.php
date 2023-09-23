<?php

namespace App\Http\Controllers;

use App\Jobs\CreateDocumentJob;
use App\Models\Collection;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    public function documentCreationStatus($jobID,Request $request)
    {
        // Validation for incoming request
        $validator = Validator::make($request->all(), [
            'jobID' => 'required|string'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors(), 'status' => false],404);
        }
    
        try {
            // Fetch the status of the job from the cache
            $jobStatus = Cache::get($request->jobID);
    
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
    // Validation for incoming request
    $validator = Validator::make($request->all(), [
        'document_id' => 'required|numeric|exists:documents,id'
    ]);

    if ($validator->fails()) {
        return response()->json(['message' => $validator->errors(), 'status' => false], 404);
    }

    try {
        // Fetch the Document by ID
        $document = Document::find($request->document_id);

        if (!$document) {
            return response()->json(['message' => 'Document not found', 'status' => false], 404);
        }

        // Check Authorization token
        $collection = $this->checkAuthToken($document->collection_id, $request);

        if (!$collection) {
            return response()->json(['message' => 'Invalid authToken', 'status' => false], 403);
        }

        // Check if the Document belongs to the Collection
        if ($document->collection_id !== $collection->id) {
            return response()->json(['message' => 'Document does not belong to this collection', 'status' => false], 403);
        }

        return response()->json([
            'document_id' => $document->id,
            'content' => $document->content,
            'content' => $document->content,
            'content_tokens' => $document->content_tokens,
            'meta' => $document->meta,
            'embeds' => $document->embeds,
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
            'collection_id' => 'required|numeric|exists:collections,id',
            'per_page' => 'nullable|numeric|min:1|max:100',
            'page' => 'nullable|numeric|min:1'
        ]);
    
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors(), 'status' => false],404);
        }
    
        // Check Authorization token
        $collection = $this->checkAuthToken($request->collection_id, $request);
        if (!$collection) {
            return response()->json(['message' => 'Invalid authToken or collection ID.', 'status' => false], 403);
        }
    
        try {
            // Number of documents per page, with a default value
            $perPage = $request->input('per_page', 50);
    
            // Fetch documents and apply pagination
            $documents = Document::where('collection_id', $request->collection_id)
                ->select('id as document_id', 'content', 'meta', 'created_at', 'updated_at')
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
        $collection = Collection::where(['id'=>$collection_id, 'authToken'=> $token])->first();
        if($collection){
            return $collection;
        }else{
            return false;
        }

    }
    //method should be delete
    public function delete($documentId, Request $request) {
        try {
            // $documentId = $request->input('document_id');
            // Fetch the Document by ID
            $document = Document::find($documentId);
    
            if (!$document) {
                return response()->json(['message' => 'Document not found', 'status' => false], 404);
            }

            $collection = $this->checkAuthToken($document->collection_id, $request);
            if (!$collection) {
                return response()->json(['message' => 'Invalid authToken', 'status' => false], 403);
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
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'collection_id' => 'required|numeric|exists:collections,id',
            'content' => 'required_without_all:url,file',
            'url' => 'required_without_all:content,file|url',
            'file' => 'required_without_all:content,url|mimes:txt,doc,docx,md,ppt,pptx,pdf,xls,xlsx,csv,html,json,msg,xml,eml,jpeg,png,jpg,odt,epub,tsv,rst,rtf',
            'meta' => 'nullable|json',
            'disable_splitter' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors(), 'status' => false], 404);
        }
        $collection = $this->checkAuthToken($request->collection_id, $request);        
        if (!$collection) {
            return response()->json(['message' => 'Authorization header is missing.', 'status' => false], 403);
        }

        $jobID = (string) Str::uuid();

        Cache::put($jobID, 'inprogress', 60 * 60);

        $content = $request->content;
        $url = $request->url;
        $file = $request->file('file');
        $meta = $request->meta;
        $disable_splitter = $request->disable_splitter ?? false;

        // Dispatch the job
        CreateDocumentJob::dispatch([
            'jobID' => $jobID,
            'collection_id' => $request->collection_id,
            'content' => $content,
            'url' => $url,
            'file' => $file,
            'meta' => $meta,
            'disable_splitter' => $disable_splitter
        ]);

        return response()->json(['jobID' => $jobID, 'status' => true]);
    }
}
