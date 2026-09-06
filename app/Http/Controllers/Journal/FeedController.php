<?php

namespace App\Http\Controllers\Journal;

use App\Services\Feed;
use Statamic\Facades\Entry;

class FeedController
{
    public function __invoke(string $format)
    {
        $items = Entry::query()
            ->where('collection', 'posts')
            ->whereStatus('published')
            ->get()
            ->merge(
                Entry::query()
                    ->where('collection', 'streams')
                    ->whereStatus('published')
                    ->get()
            )
            ->sortByDesc(fn ($entry) => $entry->date())
            ->values()
            ->map(fn ($entry) => $entry->collection()->handle() === 'posts'
                ? Feed::postItem($entry)
                : Feed::streamItem($entry));

        return Feed::make(
            'Journal',
            'Feed of all posts and streams.',
            $items,
            $format
        );
    }
}
