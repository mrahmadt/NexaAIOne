<?php

use Illuminate\Support\Facades\Facade;

return [
    'summarization_model' => env('MEMORY_OPENAI_SUMMARIZATION_MODEL', null),
    'summarization_prompt' => env('MEMORY_OPENAI_SUMMARIZATION_PROMPT'),
];
