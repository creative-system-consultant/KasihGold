<?php
return [
    'merchantId' => env('FIUU_MERCHANT_ID'),
    'key'  => env('FIUU_VERIFY_KEY'),
    'secret' => env('FIUU_SECRET_KEY'),
    'url' => env('FIUU_URL'),
    'sandbox' => env('FIUU_SANDBOX', true),
    'api_url' => env('FIUU_SANDBOX', true) 
        ? env('FIUU_API_URL_SANDBOX')
        : env('FIUU_API_URL'),
    'payment_url' => env('FIUU_SANDBOX', true)
        ? env('FIUU_PAYMENT_URL_SANDBOX')
        : env('FIUU_PAYMENT_URL'),
];
