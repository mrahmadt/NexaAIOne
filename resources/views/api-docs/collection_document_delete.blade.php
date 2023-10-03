<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Collection - Delete Document</title>
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
            <h1 class="font-bold text-2xl">API for Deleting Documents in Collection</h1>
            <div class="mt-2">A Collection serves as a structured data store for text-based documents. This API allows you to delete a document in a specified collection.</div>
            
            <div class="text-sm m-5 inline-flex items-center rounded-md bg-gray-50 px-2 py-1 font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                DELETE {!!str_replace('%20','',route('api.document.delete',['document_id'=>" "]))!!}<div class="text-red-700">[document_id]</div>
            </div>
            
            
            <div class="grid grid-cols-2">
                <div>
                    <h2 class="font-bold text-xl mt-4">API Options</h2>
                    <div class="mt-2">The following option is available for this API:</div>
                    
                    <!-- document_id -->
                    <div class="mt-4 p-4 text-sm border-b">
                        <span class="text-base font-bold">document_id</span>
                        <span class="text-gray-500">numeric</span>
                        <span class="text-red-500">Required</span>
                        <div class="text-gray-500">
                            The ID of the document to be deleted.
                        </div>
                    </div>
                </div>
            
            <div>
    <h2 class="font-bold text-xl mt-4">Request Examples</h2>

            <!-- Node.js -->
            <h3 class="mt-4 text-lg font-semibold">Node.js</h3>
            <pre><code class="language-javascript">
const axios = require('axios');

axios.delete('{!!str_replace('%20','',route('api.document.delete',['document_id'=>" "]))!!}<div class="text-red-700">[document_id]</div>')
    .then(response => {
        console.log(response.data);
    })
    .catch(error => {
        console.error(error);
    });
            </code></pre>

            <!-- PHP -->
            <h3 class="mt-4 text-lg font-semibold">PHP</h3>
            <pre><code class="language-php">
$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => '{!!str_replace('%20','',route('api.document.delete',['document_id'=>" "]))!!}<div class="text-red-700">[document_id]</div>',
    CURLOPT_CUSTOMREQUEST => 'DELETE',
    CURLOPT_RETURNTRANSFER => true
]);

$response = curl_exec($curl);

curl_close($curl);

echo $response;
            </code></pre>

            <!-- Python -->
            <h3 class="mt-4 text-lg font-semibold">Python</h3>
            <pre><code class="language-python">
import requests

response = requests.delete('{!!str_replace('%20','',route('api.document.delete',['document_id'=>" "]))!!}<div class="text-red-700">[document_id]</div>')

print(response.json())
            </code></pre>
        </div>
    </div>


</main>
</body>
</html>
