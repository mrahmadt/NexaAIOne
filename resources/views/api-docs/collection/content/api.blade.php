<div class="col-span-9 mx-auto">
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <h1 class="font-bold text-2xl">{{ $api->name }}</h1>
        <div class="mt-2">{{ $api->description }}</div>
        <div
            class="text-sm m-5 inline-flex items-center rounded-md bg-gray-50 px-2 py-1 font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
            POST {{ route('api.execute', ['appId' => $app->id, 'apiId' => $api->id, 'name' => $api->endpoint]) }}</div>

        <div class="grid grid-cols-2" x-data="{ showMe: true }">
            <div>
                <h2 class="font-bold text-xl" x-on:click="showMe = !showMe">API Options</h2>
                <div x-show="showMe">
                    <div class="mt-2">The following options are available for the API</div>
                    @foreach ($api->options as $group => $groupOptions)
                        @foreach ($groupOptions as $option)
                            @if (!$option['isApiOption'])
                                @continue
                            @endif
                            <div class="mt-4 p-4 text-sm border-b">
                                <div><span class="text-base font-bold">{{ $option['name'] }}</span>
                                    <span class="ps-2 text-gray-400">{{ $option['type'] }}</span>
                                    <span
                                        class="ps-2 {{ $option['required'] ? 'text-red-500' : 'text-green-500' }}">{{ $option['required'] ? 'Required' : 'Optional' }}</span>
                                </div>
                                <div class="text-gray-500">{{ $option['desc'] }}</div>
                                @if (isset($option['default']) && $option['default'])
                                    <div class="text-gray-500 pt-2"><b>Default:</b> {{ $option['default'] }}</div>
                                @endif
                                @if (isset($option['options']) && $option['options'])
                                    <div class="text-gray-500 pt-2"><b>Options:</b><br>
                                        <div class="ps-2">{!! implode('<br>', array_keys($option['options'])) !!}</div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
            <div x-show="showMe">
                <div class="rounded-lg bg-gray-800 ">
<div class="code text-white text-sm px-4 py-4 language-bash">curl {{ route('api.execute', ['appId' => $app->id, 'apiId' => $api->id, 'name' => $api->endpoint]) }} \
    -H <span class="text-green-600">"Content-Type: application/json"</span> \
    -H <span class="text-green-600">"Authorization: Bearer</span> <span class="text-red-500">$AUTH_TOKEN"</span> \
    -d <span class="text-green-600 whitespace-pre-wrap">'{{ json_encode($testAPIOptions, JSON_PRETTY_PRINT) }}'
</div>
                </div>
            </div>
        </div>
    </div>
    <div class="p-2 ">
        <div class="mx-20 p-0 border-t"></div>

        <h2 class="pt-4 px-4 font-bold text-xl">Test API</h2>

        <div x-data="init()">
            <script>
                function init() {
                    return {
                        showTab: 'options',
                        submitText: 'Submit',
                        showErrorOptions: false,
                        errorMessageOptions: 'error',
                        formLoading: false,
                        submitAPI() {
                            const authToken = document.getElementById('authToken').value;
                            this.showErrorOptions = false;
                            if (authToken == '') {
                                this.showErrorOptions = true;
                                this.errorMessageOptions = 'Please enter an authorization token';
                                return;
                            }
                            this.formLoading = true;
                            this.submitText = 'Loading...';

                            fetch("http://localhost/api/v1/app/appId/apiId/name", {
                                    method: "POST",
                                    cache: "no-cache",
                                    headers: {
                                        'Authorization': "Bearer " + authToken,
                                        'Content-Type': "application/json",
                                        'Accept': "application/json",
                                    },
                                    body: content.text,
                                })
                                .then(response => {
                                    const contentType = response.headers.get('Content-Type');
                                    if (contentType && contentType.indexOf('application/json') !== -1) {
                                        response.json().then(data => {
                                            apiResponseContent = {
                                                json: data
                                            }
                                            document.getElementById("setContent").click();
                                            this.showTab = 'response';

                                            // document.getElementById('apiResponse').innerHTML = JSON.stringify(data, null, 2);
                                            // hljs.highlightElement(document.getElementById('apiResponse'));
                                        });
                                    } else {
                                        response.text().then(text => {
                                            apiResponseContent = {
                                                text: "{}"
                                            }
                                            document.getElementById("setContent").click();
                                            document.getElementById('apiResponseText').innerHTML = text;
                                            this.showTab = 'response';

                                            // hljs.highlightElement(document.getElementById('apiResponse'));
                                        });
                                    }
                                    // document.querySelectorAll('pre > code').forEach(function(el) {
                                    //     hljs.highlightElement(el);
                                    // });
                                })
                                .catch((error) => {
                                    this.showErrorOptions = true;
                                    this.errorMessageOptions = `Error: ${error.message}`;
                                    console.log(error);
                                })
                                .finally(() => {
                                    this.formLoading = false;
                                    this.submitText = "Submit";
                                });
                        },
                    }
                }
                let contentJson = {!! $testAPIOptionsJson !!};
                let content = {
                    text: JSON.stringify(contentJson, null, 2)
                    // text: "{\n\"response\": \"null\"\n}"
                }
                let apiResponseContent = {
                    text: "{\n\"response\": \"null\"\n}"
                }
                hljs.highlightAll();
            </script>
            <ul class="mt-5 mb-2 flex flex-wrap text-base  text-center text-gray-700 border-b border-gray-200">
                <li class="mr-2" x-on:click="showTab='options'">
                    <a href="#" aria-current="page"
                        :class="showTab == 'options' ? 'shadow-inner border-b-2 border border-b-gray-500' : ''"
                        class="inline-block p-4 text-blue-800 rounded-t-lg">API Options</a>
                </li>
                <li class="mr-2" x-on:click="showTab='response'">
                    <a href="#"
                        :class="showTab == 'response' ? 'shadow-inner border-b-2 border border-b-gray-500' : ''"
                        class="inline-block p-4 text-blue-800 rounded-t-lg">API Response</a>
                </li>
            </ul>
            <div x-show="showTab=='options'" class="pb-6">
                <div id="showErrorOptions" x-show="showErrorOptions" x-text="errorMessageOptions"
                    class="py-2 text-red-800 text-sm">Please enter an authorization token</div>
                <div class="my-2 flex place-items-center p-2">
                    <div class="place-items-center"><label for="authToken"
                            class="text-sm pe-2 font-medium  text-gray-900 ">Authorization Token:</label></div>
                    <div class="grow mt-2 rounded-md shadow-sm">
                        <input type="text" name="authToken" id="authToken"
                            class="block w-full rounded-md border-0 py-1.5 pl-2 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                    <div x-on:click="submitAPI()" class="px-2 place-items-center"><button
                            class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg shadow-lg"
                            :disabled="formLoading" x-text="submitText">Submit</button>
                    </div>
                </div>
                <div id="apiOptionsDiv" class="p-2 h-96" data-gramm="false" data-gramm_editor="false"
                    data-enable-grammarly="false"></div>
            </div>
            <div id="tabResponse" x-show="showTab=='response'"><button class="sm:hidden" id="setContent">Set
                    content</button>
                <div id="apiResponse" class="h-96 p-2"></div>
                <div id="apiResponseText" class="p-2"></div>
            </div>
        </div>

        <div class="p-10"></div>
        <script type="module">
            import {
                JSONEditor
            } from 'https://unpkg.com/vanilla-jsoneditor/index.js'
            const editor = new JSONEditor({
                target: document.getElementById('apiOptionsDiv'),
                props: {
                    content,
                    onRenderMenu: (items, context) => {
                        return items.filter(v => v.text !== "table" && v.type === "button");
                    },
                    mode: 'text',
                    navigationBar: false,
                    mainMenuBar: true,
                    statusBar: true,
                    onChange: (updatedContent, previousContent, {
                        contentErrors,
                        patchResult
                    }) => {
                        content = updatedContent;
                    }
                }
            })
            const editorResponse = new JSONEditor({
                target: document.getElementById('apiResponse'),
                props: {
                    apiResponseContent,
                    onRenderMenu: (items, context) => {
                        return items.filter(v => v.text !== "table" && v.type === "button");
                    },
                    mode: 'text',
                    navigationBar: false,
                    mainMenuBar: true,
                    statusBar: true,
                    readOnly: true,
                }
            })
            document.getElementById('setContent').onclick = function() {
                editorResponse.set(apiResponseContent)
            }
        </script>

    </div>
</div>
