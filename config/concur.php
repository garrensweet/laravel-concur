<?php

return [
    'client_id'         => env('CONCUR_CLIENT_ID', ''),
    'client_secret'     => env('CONCUR_SECRET_ID', ''),
    'grant_type'        => env('CONCUR_GRANT_TYPE', ''),
    'api_url_prefix'    => env('CONCUR_API_URL_PREFIX', ''),
    'username'          => env('CONCUR_USERNAME', ''),
    'password'          => env('CONCUR_PASSWORD', ''),
    'scope'             => env('CONCUR_SCOPE', '')
];