<?php

namespace App\Http\Controllers\Blog\Category;

use App\Category;
use App\Services\MetaBag;

class IndexController
{
    public function __invoke(MetaBag $meta, Category $category, int $page = 1)
    {
        $meta->title = sprintf('Posts about "%s" | Blog', $category->slug);

        $posts = $category->posts()
            ->paginate($page)
            ->withRoute('blog.category.index', compact('category'));

        return view('pages.blog.category', compact('category', 'posts'));
    }
}
