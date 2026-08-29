<?php

use App\Job;
use App\Post;
use App\Stream;
use Spatie\Sheets\ContentParsers\JsonParser;
use Spatie\Sheets\ContentParsers\MarkdownWithFrontMatterParser;
use Spatie\Sheets\ContentParsers\YamlParser;
use Spatie\Sheets\PathParsers\SlugWithDateParser;

return [
    'default_collection' => 'static',

    'collections' => [
        'static',
        'posts' => [
            'disk' => 'posts',
            'sheet_class' => Post::class,
            'path_parser' => SlugWithDateParser::class,
            'content_parser' => MarkdownWithFrontMatterParser::class,
            'extension' => 'md',
        ],
        'streams' => [
            'disk' => 'streams',
            'sheet_class' => Stream::class,
            'path_parser' => SlugWithDateParser::class,
            'content_parser' => YamlParser::class,
            'extension' => 'yml',
        ],
        'jobs' => [
            'disk' => 'jobs',
            'sheet_class' => Job::class,
            'content_parser' => YamlParser::class,
            'extension' => 'yml',
        ],
        'hacktoberfest' => [
            'disk' => 'hacktoberfest',
            'content_parser' => YamlParser::class,
            'extension' => 'yml',
        ],
        'strava' => [
            'disk' => 'strava',
            'content_parser' => JsonParser::class,
            'extension' => 'json',
        ],
    ],
];
