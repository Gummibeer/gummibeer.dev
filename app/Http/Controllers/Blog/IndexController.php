<?php

namespace App\Http\Controllers\Blog;

use App\Services\OgImage;
use Statamic\Facades\Entry;

class IndexController
{
    public function __invoke(OgImage $ogImage, int $page = 1)
    {
        $posts = Entry::query()
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
            ->paginate($page)
            ->withRoute('blog.index', [], '/blog');

        return view('pages.blog.index', [
            'posts' => $posts,
            'title' => 'Blog',
            'image' => $ogImage->forCollectionMount('posts'),
        ]);
    }
}
