<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Collection - List Documents</title>
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
    </main>
    

</body>
</html>
