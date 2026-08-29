<?php

namespace App\Http\Controllers\Blog;

use App\Post;
use App\Services\MetaBag;
use App\Stream;

class IndexController
{
    public function __invoke(MetaBag $meta, int $page = 1)
    {
        $meta->title = 'Blog';
        $meta->image = mix('images/og/static/blog.png');

        $posts = Post::all()
            ->merge(Stream::all())
            ->sortByDesc('date')
            ->paginate($page)
            ->withRoute('blog.index');

        return view('pages.blog.index', compact('posts'));
    }
}
