        <div class="col-span-9 mx-auto">
        <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
            <h1 class="font-bold text-2xl">API for Getting Documents in Collection</h1>
            <div class="mt-2">
                A Collection serves as a structured data store for text-based documents. This API allows you to retrieve a document from a specified collection.
            </div>
            
            <div class="text-sm m-5 inline-flex items-center rounded-md bg-gray-50 px-2 py-1 font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                GET {!!str_replace('%20','',route('api.document.get',['document_id'=>" "]))!!}<div class="text-red-700">[document_id]</div>
            </div>
    
            <div class="grid grid-cols-2">
                <div>
                    <h2 class="font-bold text-xl mt-4">API Options</h2>
                    <div class="mt-2">The following options are available for this API:</div>
    
                    <!-- document_id -->
                    <div class="mt-4 p-4 text-sm border-b">
                        <span class="text-base font-bold">document_id</span>
                        <span class="text-gray-500">numeric</span>
                        <span class="text-red-500">Required</span>
                        <div class="text-gray-500">
                            The ID of the document you want to retrieve from the collection.
                        </div>
                    </div>
                </div>
                <div>
                    <h2 class="font-bold text-xl mt-4">Request Examples</h2>
    
                    <!-- Node.js -->
                    <h3 class="mt-4 text-lg font-semibold">Node.js</h3>
                    <pre><code class="language-javascript">
    const axios = require('axios');
    
    const document_id = 1;
    const authToken = 'your_auth_token_here';
    
    axios.get(`{!!str_replace('%20','',route('api.document.get',['document_id'=>" "]))!!}<div class="text-red-700">[document_id]</div>`, {
      headers: {
        'Authorization': `Bearer ${authToken}`
      }
    })
    .then(response => {
      console.log(response.data);
    })
    .catch(error => {
      console.log(error);
    });
                    </code></pre>
    
                    <!-- PHP -->
                    <h3 class="mt-4 text-lg font-semibold">PHP</h3>
                    <pre><code class="language-php">
    $document_id = 1;
    $authToken = 'your_auth_token_here';
    
    $options = [
        'http' => [
            'header' => "Authorization: Bearer ".$authToken
        ]
    ];
    
    $context = stream_context_create($options);
    $response = file_get_contents("{!!str_replace('%20','',route('api.document.get',['document_id'=>" "]))!!}<div class="text-red-700">[document_id]</div>, false, $context);
    
    if ($response !== FALSE) {
        var_dump(json_decode($response, true));
    }
                    </code></pre>
    
                    <!-- Python -->
                    <h3 class="mt-4 text-lg font-semibold">Python</h3>
                    <pre><code class="language-python">
    import requests
    
    document_id = 1
    authToken = 'your_auth_token_here'
    
    headers = {'Authorization': f'Bearer {authToken}'}
    
    response = requests.get(f"{!!str_replace('%20','',route('api.document.get',['document_id'=>" "]))!!}<div class="text-red-700">[document_id]</div>", headers=headers)
    
    if response.status_code == 200:
        print(response.json())
                    </code></pre>
                </div>
            </div>
        </div>
    </div>