<?php

return [
    'api_url'     => env('AI_API_URL', 'https://api.abdalgani.com/openai/deployments/gemini-3-flash-preview/chat/completions'),
    'api_key'     => env('AI_API_KEY'),
    'model'       => env('AI_MODEL', 'gemini-3-flash-preview'),
    'max_tokens'  => env('AI_MAX_TOKENS', 4096),
    'temperature' => env('AI_TEMPERATURE', 0.7),
];
