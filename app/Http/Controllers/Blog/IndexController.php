<?php

namespace App\Http\Controllers\Blog;

use App\Services\MetaBag;
use Statamic\Facades\Entry;

class IndexController
{
    public function __invoke(MetaBag $meta, int $page = 1)
    {
        $meta->title = 'Blog';
        $meta->image = asset('images/og/static/blog.png');

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
            ->withRoute('blog.index');

        return view('pages.blog.index', compact('posts'));
    }
}
