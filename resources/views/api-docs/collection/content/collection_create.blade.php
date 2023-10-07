<div class="col-span-9 mx-auto">
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <h1 class="font-bold text-2xl">API for Creating App Collections</h1>
        <div class="mt-2">
            This API allows you to create a new collection for a specified app.
        </div>
        <div
            class="text-sm m-5 inline-flex items-center rounded-md bg-gray-50 px-2 py-1 font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
            POST {!! route('api.app.collection.create') !!}
        </div>
        <div class="grid grid-cols-2">
            <div>
                <h2 class="font-bold text-xl mt-4">API Options</h2>
                <div class="mt-2">The following options are available for this API:</div>

                <!-- app_id -->
                <div class="mt-4 p-4 text-sm border-b">
                    <span class="text-base font-bold">app_id</span>
                    <span class="text-gray-500">numeric</span>
                    <span class="text-red-500">Required</span>
                    <div class="text-gray-500">
                        The ID of the app for which the collection is being created.
                    </div>
                </div>

                <!-- name -->
                <div class="mt-4 p-4 text-sm border-b">
                    <span class="text-base font-bold">name</span>
                    <span class="text-gray-500">string (max: 150)</span>
                    <span class="text-red-500">Required</span>
                    <div class="text-gray-500">
                        The name of the collection.
                    </div>
                </div>

                <!-- description -->
                <div class="mt-4 p-4 text-sm border-b">
                    <span class="text-base font-bold">description</span>
                    <span class="text-gray-500">string (max: 255)</span>
                    <span class="text-gray-500">Optional</span>
                    <div class="text-gray-500">
                        Description of the collection.
                    </div>
                </div>

                <!-- context_prompt -->
                <div class="mt-4 p-4 text-sm border-b">
                    <span class="text-base font-bold">context_prompt</span>
                    <span class="text-gray-500">string (max: 255)</span>
                    <span class="text-gray-500">Optional</span>
                    <div class="text-gray-500">
                        The context prompt to use for the collection.
                    </div>
                    <div class="text-gray-500">
                        <br><b>Example:</b> Answer the following Question based on the Context only. Only answer from
                        the Context. Try to provide a reference with your answer. If you don't know the answer mention
                        that you couldn't find the
                        answer<br>CONTEXT:@{{ context }}<br><br>Question:@{{ userMessage }}
                    </div>
                </div>

                <!-- defaultTotalReturnDocuments -->
                <div class="mt-4 p-4 text-sm border-b">
                    <span class="text-base font-bold">defaultTotalReturnDocuments</span>
                    <span class="text-gray-500">numeric</span>
                    <span class="text-gray-500">Optional</span>
                    <div class="text-gray-500">
                        Max Documents to share with AI service for each API call?
                    </div>
                </div>

                <!-- loader_id -->
                <div class="mt-4 p-4 text-sm border-b">
                    <span class="text-base font-bold">loader_id</span>
                    <span class="text-gray-500">numeric</span>
                    <span class="text-gray-500">Optional</span>
                    <div class="text-gray-500">
                        ID for the loader to use. Document loader provides a "load/download" and "extract text" method
                        for any specified URL or file.
                    </div>
                </div>

                <!-- splitter_id -->
                <div class="mt-4 p-4 text-sm border-b">
                    <span class="text-base font-bold">splitter_id</span>
                    <span class="text-gray-500">numeric</span>
                    <span class="text-gray-500">Optional</span>
                    <div class="text-gray-500">
                        ID for the splitter to use. Often times you want to split large text into smaller chunks to
                        better work with language models. TextSplitters are responsible for splitting up large text into
                        smaller documents.
                    </div>
                </div>

                <!-- embedder_id -->
                <div class="mt-4 p-4 text-sm border-b">
                    <span class="text-base font-bold">embedder_id</span>
                    <span class="text-gray-500">numeric</span>
                    <span class="text-gray-500">Optional</span>
                    <div class="text-gray-500">
                        ID for the embedder to use. Embeddings create a vector representation of a document to do things
                        like semantic search where we look for pieces of text that are most similar to an API query
                    </div>
                </div>

            </div>
            <div>
                <h2 class="font-bold text-xl mt-4">Request Examples</h2>

                <!-- Node.js -->
    <h3 class="mt-4 text-lg font-semibold">Node.js</h3>
    <pre><code class="language-javascript">
const axios = require('axios');

const data = {
    app_id: 'YOUR_APP_ID',
    name: 'YOUR_COLLECTION_NAME',
    // ... add other fields as necessary
};

axios.post('{!! route('api.app.collection.create') !!}', data, {
    headers: {
        'Authorization': 'Bearer YOUR_TOKEN_HERE'
    }
})
.then(response => {
    console.log(response.data);
})
.catch(error => {
    console.error('Error:', error.response.data);
});
    </code></pre>

    <!-- PHP -->
    <h3 class="mt-4 text-lg font-semibold">PHP</h3>
    <pre><code class="language-php">
$ch = curl_init();

$data = [
    'app_id' => 'YOUR_APP_ID',
    'name' => 'YOUR_COLLECTION_NAME',
    // ... add other fields as necessary
];

$headers = [
    'Authorization: Bearer YOUR_TOKEN_HERE'
];

curl_setopt($ch, CURLOPT_URL, '{!! route('api.app.collection.create') !!}');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}

curl_close($ch);
    </code></pre>

    <!-- Python -->
    <h3 class="mt-4 text-lg font-semibold">Python</h3>
    <pre><code class="language-python">
import requests

url = "{!! route('api.app.collection.create') !!}"
headers = {
    'Authorization': 'Bearer YOUR_TOKEN_HERE'
}
data = {
    'app_id': 'YOUR_APP_ID',
    'name': 'YOUR_COLLECTION_NAME',
    # ... add other fields as necessary
}

response = requests.post(url, headers=headers, data=data)

print(response.json())
    </code></pre>
            </div>

        </div>
    </div>
</div>
