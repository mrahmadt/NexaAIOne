<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Collection - {{$title}}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/atom-one-dark.min.css"
        integrity="sha512-Jk4AqjWsdSzSWCSuQTfYRIF84Rq/eV0G2+tu07byYwHcbTGfdmLrHjUSwvzp5HvbiqK4ibmNwdcG49Y5RGYPTg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
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
    <script>
        hljs.highlightAll();
    </script>
</head>

<body class="bg-white">
    <main class="grid grid-cols-10">
        @include('api-docs.collection.nav')
        @include('api-docs.collection.content.'. $contentView)
    </main>
</body>

</html>
