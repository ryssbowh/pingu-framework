<?php

return [
    'minPhpVersion' => '7.0.0',
    'requirements' => [
        'php' => [
            'openssl',
            'pdo',
            'mbstring',
            'tokenizer',
            'JSON',
            'cURL',
            'xml'
        ],
        'apache' => [
            'mod_rewrite'
        ]
    ],
    'permissions' => [
        'storage/framework/'     => '775',
        'storage/logs/'          => '775',
        'bootstrap/cache/'       => '775'
    ],
    'env' => [
        'APP_NAME' => [
            'label' => 'Site name',
            'type' => 'text',
            'validation' => 'required',
            'messages' => [
                'APP_NAME.required' => 'Site name is required'
            ]
        ],
        'APP_ENV' => [
            'label' => 'Environment',
            'type' => 'select',
            'values' => ['local' => 'Local', 'production' => 'Production'],
            'validation' => 'required|in:local,production',
            'messages' => [
                'APP_ENV.required' => 'Environment is required',
                'APP_ENV.in' => 'Environment is invalid'
            ]
        ],
        'APP_DEBUG' => [
            'label' => 'Debug mode',
            'type' => 'select',
            'values' => ['true' => 'Yes', 'false' => 'No'],
            'validation' => 'required|in:true,false',
            'messages' => [
                'APP_DEBUG.required' => 'Debug mode is required',
                'APP_DEBUG.in' => 'Debug mode is invalid'
            ]
        ],
        'APP_URL' => [
            'label' => 'Site url',
            'type' => 'text',
            'validation' => 'required|url',
            'messages' => [
                'APP_URL.required' => 'Site name is required',
                'APP_URL.url' => 'Site name is an invalid url'
            ]
        ]
    ],
    'drivers' => [
        'mysql' => 'MySql'
    ],
    'minNpmVersion' => '6.0.0'
];