<?php

use Illuminate\Support\Facades\Facade;

return [
    'api_key' => env('OPENAI_API_KEY'),
    'base_uri' => env('OPENAI_BASE_URI'),
    'default_chat_model' => env('OPENAI_DEFAULT_CHAT_MODEL', 'gpt-3.5-turbo'),
    'default_embeddings_model' => env('OPENAI_DEFAULT_EMBEDDINGS_MODEL', 'text-embedding-ada-002'),
    'memory_summarization_model' => env('MEMORY_OPENAI_SUMMARIZATION_MODEL', null),
    'memory_summarization_prompt' => env('MEMORY_OPENAI_SUMMARIZATION_PROMPT'),
];
