<?php

namespace App\Http\Controllers\Blog\Category;

use App\Services\Feed;
use Statamic\Facades\Entry;
use Statamic\Facades\Term;

class FeedController
{
    public function __invoke(string $category, string $format)
    {
        $category = Term::find('category::'.$category);

        abort_unless($category, 404);

        $items = Entry::query()
            ->where('collection', 'posts')
            ->whereStatus('published')
            ->whereTaxonomy($category->id())
            ->get()
            ->sortByDesc(fn ($entry) => $entry->date())
            ->values()
            ->map(fn ($post) => Feed::postItem($post));

        return Feed::make(
            $category->title(),
            'Feed of all "'.$category->title().'" posts.',
            $items,
            $format
        );
    }
}
