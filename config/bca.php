<?php

return [
    'client_id' => env('BCA_CLIENT_ID', null),
    'client_secret' => env('BCA_CLIENT_SECRET', null),
    'api_url' => env('BCA_API_URL', 'https://sandbox.bca.co.id:443'),
    'uri' => array(
        'auth' => '/api/oauth/token',
    ),
];