<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default search index
    |--------------------------------------------------------------------------
    |
    | This option controls the search index that gets queried when performing
    | search functions without explicitly selecting another index.
    |
    */

    'default' => env('STATAMIC_DEFAULT_SEARCH_INDEX', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Search Indexes
    |--------------------------------------------------------------------------
    |
    | Here you can define all of the available search indexes.
    |
    */

    'indexes' => [

        'default' => [
            'driver' => 'local',
            'searchables' => 'content',
            'fields' => ['title'],
        ],

        'blog' => [
            'driver' => 'local',
            'searchables' => ['collection:posts', 'collection:streams'],
            'fields' => ['title', 'description', 'category', 'content', 'channel_name', 'transcript_text'],
            'min_characters' => 3,
            'property_weights' => [
                'title' => 4,
                'description' => 2,
                'category' => 2,
                'content' => 1,
                'channel_name' => 1,
                'transcript_text' => 1,
            ],
            'use_stemming' => true,
            'use_alternates' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Driver Defaults
    |--------------------------------------------------------------------------
    |
    | Here you can specify default configuration to be applied to all indexes.
    |
    */

    'drivers' => [

        'local' => [
            'path' => storage_path('statamic/search'),
        ],

        'algolia' => [
            'credentials' => [
                'id' => env('ALGOLIA_APP_ID', ''),
                'secret' => env('ALGOLIA_SECRET', ''),
            ],
        ],

    ],

    'defaults' => [
        'fields' => ['title'],
    ],

    'queue' => env('STATAMIC_SEARCH_QUEUE'),

    'queue_connection' => env('STATAMIC_SEARCH_QUEUE_CONNECTION'),

    'queue_timeout' => env('STATAMIC_SEARCH_QUEUE_TIMEOUT'),

    'chunk_size' => 100,

];
