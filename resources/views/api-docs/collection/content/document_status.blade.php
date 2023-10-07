        <div class="col-span-9 mx-auto">
        <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
            <h1 class="font-bold text-2xl">API for Checking Document Creation Status in Collection</h1>
            <div class="mt-2">A Collection serves as a structured data store for text-based documents. This API allows you to check the status of a document creation job in a specified collection.</div>
    
            <div class="text-sm m-5 inline-flex items-center rounded-md bg-gray-50 px-2 py-1 font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                GET {!!str_replace('%20','',route('api.document.status',['jobID'=>' ']))!!}<div class="text-red-700">{jobID}</div>
            </div>
    
            <div class="grid grid-cols-2">
                <div>
                    <h2 class="font-bold text-xl mt-4">API Options</h2>
                    <div class="mt-2">The following options are available for this API:</div>
    
                    <!-- jobID -->
                    <div class="mt-4 p-4 text-sm border-b">
                        <span class="text-base font-bold">jobID</span>
                        <span class="text-gray-500">string</span>
                        <span class="text-red-500">Required</span>
                        <div class="text-gray-500">
                            The ID of the job for which the status will be fetched.
                        </div>
                    </div>
    
                </div>
    
                <div>
                    <h2 class="font-bold text-xl mt-4">Request Examples</h2>
    
                    <!-- Node.js Example -->
                    <h3 class="mt-4 text-lg font-semibold">Node.js</h3>
                    <pre><code class="language-javascript">const axios = require('axios');
    
    axios.get('{!!str_replace('%20','',route('api.document.status',['jobID'=>' ']))!!}<div class="text-red-700">{jobID}</div>')
    .then(response => {
        console.log(response.data);
    })
    .catch(error => {
        console.log(error);
    });</code></pre>
    
                    <!-- PHP Example -->
                    <h3 class="mt-4 text-lg font-semibold">PHP</h3>
                    <pre><code class="language-php">
    $url = "{!!str_replace('%20','',route('api.document.status',['jobID'=>' ']))!!}<div class="text-red-700">{jobID}</div>";
    $response = file_get_contents($url);
    echo $response;
    </code></pre>
    
                    <!-- Python Example -->
                    <h3 class="mt-4 text-lg font-semibold">Python</h3>
                    <pre><code class="language-python">import requests
    
    response = requests.get("{!!str_replace('%20','',route('api.document.status',['jobID'=>' ']))!!}<div class="text-red-700">{jobID}</div>")
    print(response.json())</code></pre>
                </div>
            </div>
        </div>
    </div>