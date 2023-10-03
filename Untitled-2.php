
I need you to build a full API documentation for documents/create API

- Build as html/blade page
- Use below information
- API url and http method (get,post..)
- Include API options in the document with description, is it required or optional
- make sure to write request & response examples using highlightjs package in different languages (curl, nodejs, php, python)


-----------------
# What is Collection
A Collection serves as a structured data store for text-based documents. You can populate this Collection either via the Collection API endpoint

The primary function of a Collection is to extend the knowledge base accessible by an AI service. When creating an API, you can specify which Collection the AI should reference for its responses. This allows you to tailor the AI's behavior and the information it draws upon, depending on the context in which it's used.

-----------------

# API http routes (e.g. http://localhost/api/v1/collections/documents/create)

Route::prefix('v1')->group(function () {
    Route::prefix('collections')->group(function () {
        Route::post('documents/create', [DocumentController::class, 'create']);
    });
});

-----------------

# API Controller

class DocumentController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'collection_id' => 'required|numeric|exists:collections,id',
            'content' => 'required_without_all:url,file',
            'url' => 'required_without_all:content,file|url',
            'file' => 'required_without_all:content,url|mimes:txt,md,xlsx,xls,csv,pdf',
            'meta' => 'nullable|json',
            'splitter_id' => 'nullable|numeric',
            'loader_id' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors(), 'status' => false], 404);
        }
        return $this->createOrUpdate($request);
    }

}

-----------------

# Similar html/blade to use for building the documentation

<body class="bg-white">
    <main>
        <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
            <h1 class="font-bold text-2xl">{{$api->name}}</h1>
            <div class="mt-2">{{$api->description}}</div>
            <div
                class="text-sm m-5 inline-flex items-center rounded-md bg-gray-50 px-2 py-1 font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                POST {{route('api.execute', ['appId'=>$app->id,'apiId'=>$api->id,'name'=>$api->endpoint])}}</div>

            <div class="grid grid-cols-2" x-data="{ showMe: true }">
                <div>
                    <h2 class="font-bold text-xl" x-on:click="showMe = !showMe">API Options</h2>
                    <div x-show="showMe">
                        <div class="mt-2">The following options are available for the API</div>
                        <div class="mt-4 p-4 text-sm border-b">
                            <div><span class="text-base font-bold">model</span> <span
                                    class="text-gray-500">string</span> <span class="text-gray-500">Optional</span>
                            </div>
                            <div class="text-gray-500">ID of the model to use. See the model endpoint compatibility
                                table for details on which models work with the Chat API.</div>
                        </div>
                        <div class="mt-4 p-4 text-sm border-b">
                            <div><span class="text-base font-bold">messages</span> <span
                                    class="text-gray-500">array</span> <span class="text-red-500">Required</span></div>
                            <div class="text-gray-500">A list of messages comprising the conversation so far. Example
                                Python code.</div>
                        </div>
                    </div>
                </div>
                <div x-show="showMe">
                    <div class="rounded-lg bg-gray-800 ">
<div class="code text-white text-sm px-4 py-4 language-bash">curl {{route('api.execute', ['appId'=>$app->id,'apiId'=>$api->id,'name'=>$api->endpoint])}} \
    -H <span class="text-green-600">"Content-Type: application/json"</span> \
    -H <span class="text-green-600">"Authorization: Bearer</span> <span
        class="text-red-500">$AUTH_TOKEN"</span> \
    -d <span class="text-green-600 whitespace-pre-wrap">'{{json_encode($testAPIOptions, JSON_PRETTY_PRINT)}}'</div>
</div>
                </div>
            </div>
        </div>