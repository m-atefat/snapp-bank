<?php

return [
    'default_sms_provider' => env('DEFAULT_SMS_PROVIDER'),
    'providers' => [
        'ghasedak' => [
            'base_url' => env('GHASEDAK_BASE_URL'),
            'api_key' => env('GHASEDAK_API_KEY')
        ],
        'kavenegar' => [
            'base_url' => env('KAVENEGAR_BASE_URL'),
            'api_key' => env('KAVENEGAR_API_KEY')
        ]
    ]
];
