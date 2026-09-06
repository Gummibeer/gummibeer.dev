<?php

namespace App\Http\Controllers\Streams;

use App\Services\Feed;
use Statamic\Facades\Entry;

class FeedController
{
    public function __invoke(string $format)
    {
        $items = Entry::query()
            ->where('collection', 'streams')
            ->whereStatus('published')
            ->get()
            ->sortByDesc(fn ($entry) => $entry->date())
            ->values()
            ->map(fn ($entry) => Feed::streamItem($entry));

        return Feed::make(
            'Streams',
            'Feed of all streams.',
            $items,
            $format
        );
    }
}
