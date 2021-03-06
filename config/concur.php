<?php

return [
    /**
     * Authentication credentials provided by Concur.
     */
    'api'         => [
        'params' => [
            'client_id'     => env('CONCUR_CLIENT_ID'),
            'client_secret' => env('CONCUR_SECRET_ID'),
            'username'      => env('CONCUR_USERNAME'),
            'password'      => env('CONCUR_PASSWORD'),
            'scope'         => env('CONCUR_SCOPE'),
        ],
        'urls'   => [
            'geolocation'   => env('CONCUR_GEOLOCATION_BASE_URL', 'https://us.api.concursolutions.com/'),
            'api_prefix'    => env('CONCUR_API_URL_PREFIX'),
            'authorization' => env('CONCUR_AUTHORIZATION_URL'),
            'signin'        => env('CONCUR_SIGNIN_URL'),
        ]
    ],
    /**
     * Application based access.
     */
    'auth' => [
        'setting' => [
            'key' => 'concur_enabled'
        ]
    ],
    /**
     * Concur Partner identifiers.
     */
    'company'     => [
        'id'               => env('CONCUR_COMPANY_ID'),
        'travel_config_id' => env('CONCUR_TRAVEL_CONFIG_ID'),
    ],
    /**
     * Map model attributes to the Concur API schemas.
     */
    'form_params' => [
        'user'   => [
            'LoginID'      => 'email',
            'FirstName'    => 'first_name',
            'LastName'     => 'last_name',
            'PrimaryEmail' => 'email'
        ],
        'travel' => [
            'profile' => [
                'CostCenter' => 'cost_center',
                'FirstName'  => 'first_name',
                'LastName'   => 'last_name',
                'LoginID'    => 'email'
            ]
        ]
    ],
    /**
     * Configuration database migrations.
     */
    'migrations'  => [
        'tenancy' => [
            'enabled'     => env('CONCUR_TENANCY_ENABLED', false),
            'foreign_key' => env('CONCUR_TENANCY_FOREIGN_KEY'),
        ]
    ]
];
