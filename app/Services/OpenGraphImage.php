<?php

namespace App\Services;

use Illuminate\Support\Str;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Contracts\Imaging\UrlBuilder;

class OpenGraphImage
{
    public const int WIDTH = 2048;

    public const int HEIGHT = 1170;

    public static function path(EntryContract $entry): string
    {
        $collection = $entry->collection()->handle();

        if ($collection === 'pages') {
            $slug = $entry->uri() === '/'
                ? 'home'
                : $entry->slug();

            return "images/og/static/{$slug}.png";
        }

        return sprintf(
            'images/og/%s/%s.%s.png',
            $collection,
            $entry->date()->format('Y-m-d'),
            $entry->slug(),
        );
    }

    public static function url(string $path): string
    {
        return url(app(UrlBuilder::class)->build(
            'images::'.Str::after($path, 'images/'),
            [],
        ));
    }
}
