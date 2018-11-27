<?php

return [
    'api'         => [
        'params' => [
            'client_id'     => env('CONCUR_CLIENT_ID'),
            'client_secret' => env('CONCUR_SECRET_ID'),
            'username'      => env('CONCUR_USERNAME'),
            'password'      => env('CONCUR_PASSWORD'),
            'scope'         => env('CONCUR_SCOPE'),
        ],
        'urls'   => [
            'api_prefix'    => env('CONCUR_API_URL_PREFIX'),
            'authorization' => env('CONCUR_AUTHORIZATION_URL'),
            'signin'        => env('CONCUR_SIGNIN_URL'),
        ]
    ],
    'company'     => [
        'id'               => env('CONCUR_COMPANY_ID'),
        'travel_config_id' => env('CONCUR_TRAVEL_CONFIG_ID'),
    ],
    'form_params' => [
        'user'   => [
            'LoginID'   => 'email',
            'FirstName' => 'first_name',
            'LastName'  => 'last_name'
        ],
        'travel' => [
            'profile' => [
                'CostCenter' => 'cost_center',
                'FirstName'  => 'first_name',
                'LastName'   => 'last_name',
                'LoginID'    => 'email'
            ]
        ]
    ]
];
