<html>
<head>
    <title>{{$app->name}} - {{$api->name ?? str_replace('_',' ',$contentView)}}</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
</head>
<body class="bg-white">
    <main class="grid grid-cols-10">
        <div class="col-span-1 bg-gray-800">
                <div class="text-white px-4 pt-10 pb-2 font-bold italic" title="Version {{$app->updated_at}}">{{$app->name}}</div>
                <div class="text-white px-4 text-xs" title="Version {{$app->updated_at}}">{{$app->updated_at}}</div>
                <div class="text-white px-4 pt-5 pb-0 font-bold underline">APIs</div>
                <div class="">
                        @foreach ($apis as $oneapi)
                            @if(isset($api->id) && $oneapi->id == $api->id)
                            <div class="py-2 bg-gray-900 text-white"><a href="{{route('api-docs.app',['appDocToken'=>$app->docToken, 'apiID'=>$oneapi->id])}}" class="font-bold rounded-md px-4 text-sm">{{$oneapi->name}}</a></div>
                            @else
                            <div class="py-2 hover:bg-gray-700 hover:text-white"><a href="{{route('api-docs.app',['appDocToken'=>$app->docToken, 'apiID'=>$oneapi->id])}}" class="text-gray-300 rounded-md px-4 text-sm">{{$oneapi->name}}</a></div>
                            @endif
                        @endforeach
                    @if($app->enableAppCollection)
                    <div class="text-white px-4 pt-5 pb-0 font-bold underline">App Collections</div>
                    <div class="">
                        @foreach ([
                                'collection_create' => 'Create Collection',
                                'collection_delete' => 'Delete Collection',
                                'document_create' => 'Create Document',
                                'document_update' => 'Update Document',
                                'document_delete' => 'Delete Document',
                                'document_get' => 'Get Document',
                                'documents_list' => 'List Documents',
                                'document_status' => 'Document Status',
                        ] as $key => $val)
                            @if(isset($contentView) && $key == $contentView)
                            <div class="py-2 bg-gray-900 text-white"><a href="/api-docs/appCollection/{{$app->docToken}}/{{$key}}" class="font-bold rounded-md px-4 text-sm">{{$val}}</a></div>
                            @else
                            <div class="py-2 hover:bg-gray-700 text-white"><a href="/api-docs/appCollection/{{$app->docToken}}/{{$key}}" class="text-gray-300 rounded-md px-4 text-sm">{{$val}}</a></div>
                            @endif
                        @endforeach
                    </div>
                    @endif
                </div>
        </div>
    @if($viewType == 'AppCollection')
    <script>
        hljs.highlightAll();
    </script>
        @include('api-docs.collection.content.'. $contentView)
    @else
        @include('api-docs.collection.content.api');
    @endif
</div>
</main>
</body>

</html>