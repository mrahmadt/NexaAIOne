
I need you to build a full API documentation for returning the document creation status API

- Use below information
- API url and http method (get,post..)
- Include API options in the document with API option name, description, is it required or optional, any default value, data type
- make sure to write request & response examples using highlightjs package in different languages (nodejs, php, python) each one in different html pre code tag


-----------------
# What is Collection
A Collection serves as a structured data store for text-based documents. You can populate this Collection either via the Collection API endpoint
The primary function of a Collection is to extend the knowledge base accessible by an AI service. When creating an API, you can specify which Collection the AI should reference for its responses. This allows you to tailor the AI's behavior and the information it draws upon, depending on the context in which it's used.

-----------------

# API http routes {{route('api.documents.list')}} to API and also in example, make sure to include any required parameters in the url

Route::prefix('v1')->group(function () {
    Route::prefix('collections')->group(function () {
        Route::get('documents/status/{jobID}', [DocumentController::class, 'documentStatus'])->name('api.document.status');
    });
});

-----------------

# API Controller

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

-----------------

# Similar html/blade to use for building the documentation

<main>
        <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
            <h1 class="font-bold text-2xl">API for Creating Documents in Collection</h1>
            <div class="mt-2">A Collection serves as a structured data store for text-based documents. This API allows you to create a document in a specified collection.</div>
            
            <div
                class="text-sm m-5 inline-flex items-center rounded-md bg-gray-50 px-2 py-1 font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                POST {!!str_replace('%20','',route('api.document.update',['document_id'=>" "]))!!}<div class="text-red-700">[document_id]</div>
            </div>
            <div class="grid grid-cols-2">
                <div>
            <h2 class="font-bold text-xl mt-4">API Options</h2>
            <div class="mt-2">The following options are available for this API:</div>
            
            <!-- collection_id -->
            <div class="mt-4 p-4 text-sm border-b">
                <span class="text-base font-bold">collection_id</span>
                <span class="text-gray-500">numeric</span>
                <span class="text-red-500">Required</span>
                <div class="text-gray-500">
                    The ID of the collection where the document will be stored.
                </div>
            </div>
            

    <!-- content -->
    <div class="mt-4 p-4 text-sm border-b">
        <span class="text-base font-bold">content</span>
        <span class="text-gray-500">string</span>
        <span class="text-red-500">Required (unless url, file, or meta provided)</span>
        <div class="text-gray-500">
            The content of the document to be updated.
        </div>
    </div>
....
</div>
<div>
    <h2 class="font-bold text-xl mt-4">Request Examples</h2>
    <h3 class="mt-4 text-lg font-semibold">Node.js</h3>
    <pre><code class="language-javascript"></code></pre>
    <h3 class="mt-4 text-lg font-semibold">PHP</h3>
    <pre><code class="language-php"></code></pre>
    <h3 class="mt-4 text-lg font-semibold">Python</h3>
    <pre><code class="language-python"></code></pre>
    
        </div>
    </div>
    </main>
