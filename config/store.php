<?php

return [
    'name' => env('STORE_NAME', 'Golden Cloth House'),
    'currency' => env('STORE_CURRENCY', 'PKR'),
    'shipping_fee' => (float) env('STORE_SHIPPING_FEE', 250),
    'free_shipping_threshold' => (float) env('STORE_FREE_SHIPPING_THRESHOLD', 5000),
    'cod_enabled' => (bool) env('STORE_COD_ENABLED', true),
    'contact_phone' => env('STORE_CONTACT_PHONE', ''),
    'whatsapp' => env('STORE_WHATSAPP', ''),
    'contact_email' => env('STORE_CONTACT_EMAIL', ''),
    'address' => env('STORE_ADDRESS', ''),
    'facebook' => env('STORE_FACEBOOK', ''),
    'instagram' => env('STORE_INSTAGRAM', ''),
    'tiktok' => env('STORE_TIKTOK', ''),
    'youtube' => env('STORE_YOUTUBE', ''),
];
