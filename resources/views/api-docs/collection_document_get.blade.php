<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Collection - Get Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/atom-one-dark.min.css" integrity="sha512-Jk4AqjWsdSzSWCSuQTfYRIF84Rq/eV0G2+tu07byYwHcbTGfdmLrHjUSwvzp5HvbiqK4ibmNwdcG49Y5RGYPTg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        div.code {
            white-space: pre;
        }
    </style>
    <script>hljs.highlightAll();</script>
</head>
<body class="bg-white">
    <nav class="bg-gray-800">
        <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
            <div class="relative flex h-16 items-center justify-between">
                <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
                    <div class="flex flex-shrink-0 items-center text-white font-bold italic">Collections</div>
                    <div class="hidden sm:ml-6 sm:block">
                        <div class="flex space-x-4">
                            <a href="/api-docs/collection/document/create" class="text-white rounded-md px-3 py-2 text-sm font-medium">Create Document</a>
                            <a href="/api-docs/collection/document/update" class="text-white rounded-md px-3 py-2 text-sm font-medium">Update Document</a>
                            <a href="/api-docs/collection/document/delete" class="text-white rounded-md px-3 py-2 text-sm font-medium">Delete Document</a>
                            <a href="/api-docs/collection/document/get" class="text-white rounded-md px-3 py-2 text-sm font-medium">Get Document</a>
                            <a href="/api-docs/collection/documents/list" class="text-white rounded-md px-3 py-2 text-sm font-medium">List Documents</a>
                            <a href="/api-docs/collection/document/status" class="text-white rounded-md px-3 py-2 text-sm font-medium">Document Status</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sm:hidden" id="mobile-menu">
            <div class="space-y-1 px-2 pb-3 pt-2">
                <a href="/api-docs/collection/document/create" class="text-white rounded-md px-3 py-2 text-sm font-medium">Create Document</a>
                <a href="/api-docs/collection/document/update" class="text-white rounded-md px-3 py-2 text-sm font-medium">Update Document</a>
                <a href="/api-docs/collection/document/delete" class="text-white rounded-md px-3 py-2 text-sm font-medium">Delete Document</a>
                <a href="/api-docs/collection/document/get" class="text-white rounded-md px-3 py-2 text-sm font-medium">Get Document</a>
                <a href="/api-docs/collection/documents/list" class="text-white rounded-md px-3 py-2 text-sm font-medium">List Documents</a>
                <a href="/api-docs/collection/document/status" class="text-white rounded-md px-3 py-2 text-sm font-medium">Document Status</a>
            </div>
        </div>
    </nav>

    <main>
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
    </main>
    </body>
</html>
