<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
// add (or merge) this block
'openai' => [
    'key'   => env('OPENAI_API_KEY'),
    'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
],


// 'openai' => [
//     'key'   => env('OPENAI_API_KEY'),
//     'model' => env('OPENAI_MODEL', 'gpt-4.1-mini'), // safe default
// ],


    //  'ai' => [
    //     'provider' => env('AI_PROVIDER', 'openai'),
    // ],
    // 'openai' => [
    //     'key'   => env('OPENAI_API_KEY'),
    //     'model' => env('OPENAI_MODEL', 'gpt-4.1-mini'),
    // ],
    // 'hf' => [
    //     'key'   => env('HF_API_KEY'),
    //     'tgi'   => rtrim(env('HF_TGI_URL', ''), '/'), // e.g. https://abc.endpoints.huggingface.cloud
    //     'model' => env('HF_MODEL', 'meta-llama/Meta-Llama-3.1-8B-Instruct'),
    // ],
    // 'ollama' => [
    //     'url'   => rtrim(env('OLLAMA_URL', 'http://localhost:11434'), '/'),
    //     'model' => env('OLLAMA_MODEL', 'llama3.1'),
    // ],
];
