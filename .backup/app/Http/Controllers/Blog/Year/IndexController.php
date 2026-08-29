<?php

namespace App\Http\Controllers\Blog\Year;

use App\Post;
use App\Services\MetaBag;

class IndexController
{
    public function __invoke(MetaBag $meta, int $year, int $page = 1)
    {
        $meta->title = sprintf('Posts from %d | Blog', $year);

        $posts = Post::all()
            ->filter(fn (Post $post): bool => $post->date->year == $year)
            ->paginate($page)
            ->withRoute('blog.year.index', compact('year'));

        return view('pages.blog.year', compact('year', 'posts'));
    }
}
