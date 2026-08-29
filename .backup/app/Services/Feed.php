<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Spatie\Feed\Feed as SpatieFeed;

class Feed extends SpatieFeed
{
    public static function make(
        string $title,
        string $description,
        Collection $items,
        string $format
    ): self {
        abort_unless(in_array($format, ['rss', 'atom']), 404);
        abort_if($items->isEmpty(), 404);

        return new static(
            $title.' | '.config('app.name'),
            $items,
            request()->url(),
            'feed::'.$format,
            $description,
            app()->getLocale()
        );
    }
}
