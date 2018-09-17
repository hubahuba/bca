<?php

return [
    /*
     * BCA Client ID
     */
    'client_id' => env('BCA_CLIENT_ID', null),

    /*
     * BCA Client Secret
     */
    'client_secret' => env('BCA_CLIENT_SECRET', null),
    /*
     * BCA Client ID
     */
    'api_key' => env('BCA_API_KEY', null),

    /*
     * BCA Client Secret
     */
    'api_secret' => env('BCA_API_SECRET', null),

    /*
     * BCA API URL
     */
    'api_url' => env('BCA_API_URL', 'https://sandbox.bca.co.id:443'),

    /*
     * BCA Endpoint URI
     */
    'endpoint' => array(
        'auth' => [
            'uri' => '/api/oauth/token',
            'method' => 'POST'
        ],
        'get_balance' => [
            'uri' => '/banking/v3/corporates/%s/accounts/%s',
            'method' => 'GET'
        ],
    ),
];