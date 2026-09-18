<?php

use Honeystone\Seo\Generators;

return [
    'generators' => [
        Generators\MetaGenerator::class => [
            'title' => env('APP_NAME'),
            'titleTemplate' => '{title}',
            'description' => '',
            'keywords' => [],
            'canonicalEnabled' => true,
            'canonical' => null,
            'robots' => [],
            'custom' => [],
        ],
        Generators\TwitterGenerator::class => [
            'enabled' => true,
            'site' => '',
            'card' => 'summary_large_image',
            'creator' => '',
            'creatorId' => '',
            'title' => '',
            'description' => '',
            'image' => '',
            'imageAlt' => '',
        ],
        Generators\OpenGraphGenerator::class => [
            'enabled' => true,
            'site' => '',
            'type' => 'website',
            'title' => '',
            'description' => '',
            'images' => [],
            'audio' => [],
            'videos' => [],
            'determiner' => '',
            'url' => null,
            'locale' => '',
            'alternateLocales' => [],
            'custom' => [],
        ],
        Generators\JsonLdGenerator::class => [
            'enabled' => true,
            'pretty' => env('APP_DEBUG'),
            'type' => 'WebPage',
            'name' => '',
            'description' => '',
            'images' => [],
            'url' => null,
            'custom' => [],
            'place-on-graph' => true,
        ],
    ],
    'sync' => [
        'url-canonical' => true,
        'keywords-tags' => false,
    ],
];
