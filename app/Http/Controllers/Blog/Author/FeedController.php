<?php

namespace App\Http\Controllers\Blog\Author;

use App\Services\Feed;
use Statamic\Facades\Entry;

class FeedController
{
    public function __invoke(string $author, string $format)
    {
        $author = Entry::query()
            ->where('collection', 'authors')
            ->where('slug', $author)
            ->first();

        abort_unless($author, 404);

        $items = Entry::query()
            ->where('collection', 'posts')
            ->whereStatus('published')
            ->where('author', $author->id())
            ->get()
            ->sortByDesc(fn ($entry) => $entry->date())
            ->values()
            ->map(fn ($post) => Feed::postItem($post));

        return Feed::make(
            (string) $author->value('title'),
            'Feed of all "'.$author->value('title').'" posts.',
            $items,
            $format
        );
    }
}
