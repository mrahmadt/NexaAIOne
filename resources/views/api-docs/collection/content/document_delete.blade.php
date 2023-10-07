        <div class="col-span-9 mx-auto">
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
</div>

</div>