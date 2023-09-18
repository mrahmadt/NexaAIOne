<?php

use Illuminate\Support\Facades\Facade;

return [
    'api_key' => env('OPENAI_API_KEY'),
    'base_uri' => env('OPENAI_BASE_URI'),
    'default_chat_model' => env('OPENAI_DEFAULT_CHAT_MODEL'),
];
