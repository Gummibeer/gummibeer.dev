<?php

return [
    'watcher' => env('STATAMIC_STACHE_WATCHER', 'auto'),
    'cache_store' => null,
    'stores' => [
        'entries' => [
            'directory' => resource_path('content'),
        ],
    ],
    'indexes' => [],
    'lock' => [
        'enabled' => true,
        'timeout' => 30,
    ],
    'warming' => [
        'parallel_processing' => env('STATAMIC_STACHE_PARALLEL_WARMING', false),
        'max_processes' => env('STATAMIC_STACHE_MAX_PROCESSES', 0),
        'min_stores_for_parallel' => env('STATAMIC_STACHE_MIN_STORES_PARALLEL', 3),
        'concurrency_driver' => env('STATAMIC_STACHE_CONCURRENCY_DRIVER', 'process'),
    ],
];
