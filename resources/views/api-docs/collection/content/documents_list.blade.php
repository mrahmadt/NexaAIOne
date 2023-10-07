        <div class="col-span-9 mx-auto">
        <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
            <h1 class="font-bold text-2xl">API for Listing Documents in a Collection</h1>
            <div class="mt-2">A Collection serves as a structured data store for text-based documents. This API allows you to list all documents in a specified collection.</div>
            
            <div class="text-sm m-5 inline-flex items-center rounded-md bg-gray-50 px-2 py-1 font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                GET {!!str_replace('%20','',route('api.documents.list',['collection_id'=>" "]))!!}<div class="text-red-700">[collection_id]</div>
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
                            The ID of the collection for which you want to list the documents.
                        </div>
                    </div>
                    
                    <!-- per_page -->
                    <div class="mt-4 p-4 text-sm border-b">
                        <span class="text-base font-bold">per_page</span>
                        <span class="text-gray-500">numeric</span>
                        <span class="text-green-500">Optional</span>
                        <span class="text-blue-500">Default: 10</span>
                        <div class="text-gray-500">
                            The number of documents to be returned per page. Minimum is 1 and maximum is 50.
                        </div>
                    </div>
    
                    <!-- page -->
                    <div class="mt-4 p-4 text-sm border-b">
                        <span class="text-base font-bold">page</span>
                        <span class="text-gray-500">numeric</span>
                        <span class="text-green-500">Optional</span>
                        <div class="text-gray-500">
                            The page number to be returned.
                        </div>
                    </div>
                </div>
                
                <div>
                    <h2 class="font-bold text-xl mt-4">Request Examples</h2>
                    <h3 class="mt-4 text-lg font-semibold">Node.js</h3>
                    <pre><code class="language-javascript">
    const axios = require('axios');
    
    async function listDocuments() {
        const url = `{!!str_replace('%20','',route('api.documents.list',['collection_id'=>" "]))!!}<div class="text-red-700">[collection_id]</div>?per_page=10&page=1`;
        const headers = {
            'Authorization': 'YOUR_AUTH_TOKEN'
        };
    
        try {
            const response = await axios.get(url, { headers });
            console.log(response.data);
        } catch (error) {
            console.error(error);
        }
    }
    
    listDocuments();
                    </code></pre>
    
                    <h3 class="mt-4 text-lg font-semibold">PHP</h3>
                    <pre><code class="language-php">
    $url = "{!!str_replace('%20','',route('api.documents.list',['collection_id'=>" "]))!!}<div class="text-red-700">[collection_id]</div>?per_page=10&page=1";
    $headers = [
        "Authorization: YOUR_AUTH_TOKEN"
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    echo $response;
                    </code></pre>
    
                    <h3 class="mt-4 text-lg font-semibold">Python</h3>
                    <pre><code class="language-python">
    import requests
    
    url = f"{!!str_replace('%20','',route('api.documents.list',['collection_id'=>" "]))!!}<div class="text-red-700">[collection_id]</div>?per_page=10&page=1"
    headers = {
        "Authorization": "YOUR_AUTH_TOKEN"
    }
    
    response = requests.get(url, headers=headers)
    
    print(response.json())
                    </code></pre>
                    
                </div>
            </div>
        </div>
    </div>