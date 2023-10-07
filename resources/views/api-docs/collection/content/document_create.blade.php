        <div class="col-span-9 mx-auto">
            <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
                <h1 class="font-bold text-2xl">API for Creating Documents in Collection</h1>
                <div class="mt-2">A Collection serves as a structured data store for text-based documents. This API
                    allows
                    you to create a document in a specified collection.</div>

                <div class="text-sm m-5 inline-flex items-center rounded-md bg-gray-50 px-2 py-1 font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                    POST {{ route('api.document.create') }}
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
                        <div class="mt-4 p-4 text-sm border-b">
                            <span class="text-base font-bold">content</span>
                            <span class="text-gray-500">string</span>
                            <span class="text-red-500">Required (unless url, file, or meta provided)</span>
                            <div class="text-gray-500">
                                The content of the document to be created.
                            </div>
                        </div>

                        <!-- url -->
                        <div class="mt-4 p-4 text-sm border-b">
                            <span class="text-base font-bold">url</span>
                            <span class="text-gray-500">string (URL)</span>
                            <span class="text-red-500">Required (unless content, file, or meta provided)</span>
                            <div class="text-gray-500">
                                The URL from which to fetch the document content.
                            </div>
                        </div>

                        <!-- file -->
                        <div class="mt-4 p-4 text-sm border-b">
                            <span class="text-base font-bold">file</span>
                            <span class="text-gray-500">file (mimes: txt, md, xlsx, xls, csv, pdf)</span>
                            <span class="text-red-500">Required (unless content, url, or meta provided)</span>
                            <div class="text-gray-500">
                                The file containing the document content.
                            </div>
                        </div>

                        <!-- meta -->
                        <div class="mt-4 p-4 text-sm border-b">
                            <span class="text-base font-bold">meta</span>
                            <span class="text-gray-500">json</span>
                            <span class="text-gray-500">Optional</span>
                            <div class="text-gray-500">
                                Metadata information for the document in JSON format.
                            </div>
                        </div>

                        <!-- splitter_id -->
                        <div class="mt-4 p-4 text-sm border-b">
                            <span class="text-base font-bold">splitter_id</span>
                            <span class="text-gray-500">numeric</span>
                            <span class="text-gray-500">Optional</span>
                            <div class="text-gray-500">
                                The ID of the splitter to be used.
                            </div>
                        </div>

                        <!-- loader_id -->
                        <div class="mt-4 p-4 text-sm ">
                            <span class="text-base font-bold">loader_id</span>
                            <span class="text-gray-500">numeric</span>
                            <span class="text-gray-500">Optional</span>
                            <div class="text-gray-500">
                                The ID of the loader to be used.
                            </div>
                        </div>
                    </div>
                    <div>
                        <h2 class="font-bold text-xl mt-4">Request Examples</h2>

<!-- Node.js Example -->
<h3 class="mt-4 text-lg font-semibold">Node.js</h3>
<pre><code class="language-javascript">
    const axios = require('axios');
    
    const data = {
        collection_id: 1,
        content: 'This is the content'
    };
    
    axios.post('{{ route('api.document.create') }}', data, {
        headers: {
            'Authorization': 'Bearer YOUR_TOKEN',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        console.log(response.data);
    })
    .catch(error => {
        console.error(error);
    });
</code></pre>

                        <!-- PHP Example -->
<h3 class="mt-4 text-lg font-semibold">PHP</h3>
<pre><code class="language-php">
    &lt;?php
    $client = new GuzzleHttp\Client();
    $response = $client-&gt;post('{{ route('api.document.create') }}', [
        'headers' => [
            'Authorization' => 'Bearer YOUR_TOKEN',
            'Content-Type' => 'application/json'
        ],
        'json' => [
            'collection_id' => 1,
            'content' => 'This is the content'
        ]
    ]);
    echo $response-&gt;getBody();
    ?&gt;
    </code></pre>

<h3 class="mt-4 text-lg font-semibold">Python</h3>
<pre><code class="language-python">
    import requests
    
    data = {
        'collection_id': 1,
        'content': 'This is the content'
    }
    
    response = requests.post('{{ route('api.document.create') }}', json=data, headers={
        'Authorization': 'Bearer YOUR_TOKEN',
        'Content-Type': 'application/json'
    })
    
    print(response.json())
</code></pre>

                    </div>
                </div>
            </div>
</div>