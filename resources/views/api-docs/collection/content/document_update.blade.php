        <div class="col-span-9 mx-auto">
        <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
            <h1 class="font-bold text-2xl">API for Updating Documents in Collection</h1>
            <div class="mt-2">A Collection serves as a structured data store for text-based documents. This API allows you to update a document in a specified collection.</div>
            <div
                class="text-sm m-5 inline-flex items-center rounded-md bg-gray-50 px-2 py-1 font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                POST {!!str_replace('%20','',route('api.document.update',['document_id'=>" "]))!!}<div class="text-red-700">[document_id]</div>
            </div>
            <div class="grid grid-cols-2">
                <div>
            <h2 class="font-bold text-xl mt-4">API Options</h2>
            <div class="mt-2">The following options are available for this API:</div>
            
            <!-- API Options -->
<div>
    
    <!-- collection_id -->
    <div class="mt-4 p-4 text-sm border-b">
        <span class="text-base font-bold">collection_id</span>
        <span class="text-gray-500">numeric</span>
        <span class="text-red-500">Required</span>
        <div class="text-gray-500">
            The ID of the collection where the document will be updated.
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
            The file containing the document to be updated.
        </div>
    </div>

    <!-- meta -->
    <div class="mt-4 p-4 text-sm border-b">
        <span class="text-base font-bold">meta</span>
        <span class="text-gray-500">json</span>
        <span class="text-red-500">Required (unless content, url, or file provided)</span>
        <div class="text-gray-500">
            Replace metadata for the document.
        </div>
    </div>

    <!-- splitter_id -->
    <div class="mt-4 p-4 text-sm border-b">
        <span class="text-base font-bold">splitter_id</span>
        <span class="text-gray-500">numeric</span>
        <span class="text-gray-500">Optional</span>
        <div class="text-gray-500">
            The ID of the splitter to use.
        </div>
    </div>

    <!-- loader_id -->
    <div class="mt-4 p-4 text-sm border-b">
        <span class="text-base font-bold">loader_id</span>
        <span class="text-gray-500">numeric</span>
        <span class="text-gray-500">Optional</span>
        <div class="text-gray-500">
            The ID of the loader to use.
        </div>
    </div>

</div>

</div>
<!-- Request Examples -->
<div>
    <h2 class="font-bold text-xl mt-4">Request Examples</h2>
    
    <!-- Node.js Example -->
    <h3 class="mt-4 text-lg font-semibold">Node.js</h3>
    <pre><code class="language-javascript">
const axios = require('axios');

const data = {
  collection_id: 1,
  content: 'Sample Content'
};

axios.put('{!!str_replace('%20','',route('api.document.update',['document_id'=>" "]))!!}<div class="text-red-700">[document_id]</div>', data)
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
&dollar;curl = curl_init();

&dollar;data = [
  'collection_id' => 1,
  'content' => 'Sample Content'
];

curl_setopt_array(&dollar;curl, [
  CURLOPT_URL => '{!!str_replace('%20','',route('api.document.update',['document_id'=>" "]))!!}<div class="text-red-700">[document_id]</div>',
  CURLOPT_CUSTOMREQUEST => 'PUT',
  CURLOPT_POSTFIELDS => &dollar;data,
  CURLOPT_RETURNTRANSFER => true
]);

&dollar;response = curl_exec(&dollar;curl);

curl_close(&dollar;curl);

echo &dollar;response;
    </code></pre>

    <!-- Python Example -->
    <h3 class="mt-4 text-lg font-semibold">Python</h3>
    <pre><code class="language-python">
import requests

data = {
  'collection_id': 1,
  'content': 'Sample Content'
}

response = requests.put('{!!str_replace('%20','',route('api.document.update',['document_id'=>" "]))!!}<div class="text-red-700">[document_id]</div>', data=data)

print(response.json())
    </code></pre>
</div>

    </div></div>
</div>