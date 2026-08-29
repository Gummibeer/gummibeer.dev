<?php

namespace App\Http\Controllers\Blog\Author;

use App\Author;
use App\Services\Feed;

class FeedController
{
    public function __invoke(Author $author, string $format)
    {
        return Feed::make(
            $author->nickname,
            'Feed of all "'.$author->nickname.'" posts.',
            $author->posts(),
            $format
        );
    }
}
