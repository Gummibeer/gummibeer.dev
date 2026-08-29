<?php

namespace App\Http\Controllers\Blog\Category;

use App\Category;
use App\Services\Feed;

class FeedController
{
    public function __invoke(Category $category, string $format)
    {
        return Feed::make(
            $category->slug,
            'Feed of all "'.$category->slug.'" posts.',
            $category->posts(),
            $format
        );
    }
}
