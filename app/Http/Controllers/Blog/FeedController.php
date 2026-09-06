<?php

namespace App\Http\Controllers\Blog;

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
            ->sortByDesc(fn ($entry) => $entry->date())
            ->values()
            ->map(fn ($entry) => Feed::postItem($entry));

        return Feed::make(
            'Blog',
            'Feed of all blog posts.',
            $items,
            $format
        );
    }
}
