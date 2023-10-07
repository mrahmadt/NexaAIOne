<div class="col-span-9 mx-auto">
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <!-- Title -->
        <h1 class="font-bold text-2xl">API for Deleting App Collection</h1>
        
        <!-- Description -->
        <div class="mt-2">
            This API allows you to delete an existing app collection.
        </div>

        <!-- API Endpoint -->
        <div class="text-sm m-5 inline-flex items-center rounded-md bg-gray-50 px-2 py-1 font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
            DELETE {!!route('api.app.collection.delete')!!}
        </div>
        
        <div class="grid grid-cols-2">
            <!-- API Options -->
            <div>
                <h2 class="font-bold text-xl mt-4">API Options</h2>
                <div class="mt-2">The following options are available for this API:</div>

                <!-- app_id -->
                <div class="mt-4 p-4 text-sm border-b">
                    <span class="text-base font-bold">app_id</span>
                    <span class="text-gray-500">numeric</span>
                    <span class="text-red-500">Required</span>
                    <div class="text-gray-500">
                        The ID of the app.
                    </div>
                </div>

                <!-- collection_id -->
                <div class="mt-4 p-4 text-sm border-b">
                    <span class="text-base font-bold">collection_id</span>
                    <span class="text-gray-500">numeric</span>
                    <span class="text-red-500">Required</span>
                    <div class="text-gray-500">
                        The ID of the collection to be deleted.
                    </div>
                </div>

                <!-- collection_authToken -->
                <div class="mt-4 p-4 text-sm border-b">
                    <span class="text-base font-bold">collection_authToken</span>
                    <span class="text-gray-500">string</span>
                    <span class="text-red-500">Required</span>
                    <div class="text-gray-500">
                        The authToken of the collection to be deleted.
                    </div>
                </div>
            </div>

            <!-- Request Examples -->
            <div>
                <h2 class="font-bold text-xl mt-4">Request Examples</h2>

                <!-- Node.js -->
                <h3 class="mt-4 text-lg font-semibold">Node.js</h3>
                <pre><code class="language-javascript">
const axios = require('axios');

const data = {
    app_id: 'YOUR_APP_ID',
    collection_id: 'YOUR_COLLECTION_ID',
    collection_authToken: 'YOUR_COLLECTION_AUTH_TOKEN'
};

axios.delete('{!! route('api.app.collection.delete') !!}', data, {
    headers: {
        'Authorization': 'Bearer YOUR_TOKEN_HERE'
    }
})
.then(response => {
    console.log(response.data);
})
.catch(error => {
    console.error('Error:', error.response.data);
});
                </code></pre>

                <!-- PHP -->
                <h3 class="mt-4 text-lg font-semibold">PHP</h3>
                <pre><code class="language-php">
$ch = curl_init();

$data = [
    'app_id' => 'YOUR_APP_ID',
    'collection_id' => 'YOUR_COLLECTION_ID',
    'collection_authToken' => 'YOUR_COLLECTION_AUTH_TOKEN'
];

$headers = [
    'Authorization: Bearer YOUR_TOKEN_HERE'
];

curl_setopt($ch, CURLOPT_URL, '{!! route('api.app.collection.delete') !!}');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}

curl_close($ch);
                </code></pre>

                <!-- Python -->
                <h3 class="mt-4 text-lg font-semibold">Python</h3>
                <pre><code class="language-python">
import requests

url = "{!! route('api.app.collection.delete') !!}"
headers = {
    'Authorization': 'Bearer YOUR_TOKEN_HERE'
}
data = {
    'app_id': 'YOUR_APP_ID',
    'collection_id': 'YOUR_COLLECTION_ID',
    'collection_authToken': 'YOUR_COLLECTION_AUTH_TOKEN'
}

response = requests.delete(url, headers=headers, data=data)

print(response.json())
                </code></pre>
            </div>
        </div>
    </div>
