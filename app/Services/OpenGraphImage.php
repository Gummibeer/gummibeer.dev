<?php

namespace App\Services;

use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Contracts\Imaging\UrlBuilder;

class OpenGraphImage
{
    public const WIDTH = 2048;

    public const HEIGHT = 1170;

    public static function url(mixed $page = null): string
    {
        return url(app(UrlBuilder::class)->build('images::'.self::path($page), []));
    }

    private static function path(mixed $page): string
    {
        if (! $page instanceof EntryContract) {
            return 'og/static/home.png';
        }

        $collection = $page->collection()->handle();

        if (in_array($collection, ['posts', 'streams'], true)) {
            return sprintf(
                'og/%s/%s.%s.png',
                $collection,
                $page->date()->format('Y-m-d'),
                $page->slug(),
            );
        }

        if ($collection === 'pages') {
            $slug = request()->path() === '/'
                ? 'home'
                : $page->slug();

            return "og/static/{$slug}.png";
        }

        return 'og/static/home.png';
    }
}
