<?php

return [
    'default' => 'gummibeer',

    'sources' => [
        'gummibeer' => [
            'domain' => env('IMGIX_DOMAIN', 'gummibeer.imgix.local'),
            'useHttps' => true,
            'signKey' => env('IMGIX_SIGN_KEY'),
            'includeLibraryParam' => false,
        ],
    ],
];
