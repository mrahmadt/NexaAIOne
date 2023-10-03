<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Collection - Create Document</title>
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
            <h1 class="font-bold text-2xl">API for Creating Documents in Collection</h1>
            <div class="mt-2">A Collection serves as a structured data store for text-based documents. This API allows you to create a document in a specified collection.</div>
            
            <div
                class="text-sm m-5 inline-flex items-center rounded-md bg-gray-50 px-2 py-1 font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                POST {{route('api.document.create')}}
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
    
    axios.post('{{route('api.document.create')}}', data, {
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
    $response = $client-&gt;post('{{route('api.document.create')}}', [
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
    
    <!-- Python Example -->
    <h3 class="mt-4 text-lg font-semibold">Python</h3>
    <pre><code class="language-python">
    import requests
    
    data = {
        'collection_id': 1,
        'content': 'This is the content'
    }
    
    response = requests.post('{{route('api.document.create')}}', json=data, headers={
        'Authorization': 'Bearer YOUR_TOKEN',
        'Content-Type': 'application/json'
    })
    
    print(response.json())
    </code></pre>
    
        </div>
    </div>
    </main>
</body>
</html>
