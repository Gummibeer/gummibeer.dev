<?php

return [
    'base_url' => config('app.url'),
    'destination' => storage_path('app/static'),
    'copy' => [
        public_path('css') => 'css',
        public_path('js') => 'js',
        public_path('images') => 'images',
        public_path('vendor') => 'vendor',
    ],
    'symlinks' => [],
    'urls' => [
        '/',
        '/resume',
        '/uses',
        '/charity',
        '/portfolio',
        '/imprint',
        '/privacy',
        '/blog',
        '/blog/search.json',
        '/sitemap.xml',
        '/robots.txt',
        '/404.html',
    ],
    'exclude' => [
        '/cp',
        '/cp/*',
    ],
    'enforce_trailing_slashes' => false,
    'pagination_route' => '{url}/{page_name}/{page_number}',
    'glide' => [
        'directory' => 'img',
        'override' => true,
    ],
    'failures' => 'errors',
];
