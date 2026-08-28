<?php

namespace App\Http\Controllers\Blog\Category;

use App\Services\MetaBag;
use Statamic\Facades\Entry;
use Statamic\Facades\Term;

class IndexController
{
    public function __invoke(MetaBag $meta, string $category, int $page = 1)
    {
        $category = Term::find('categories::'.$category);

        abort_unless($category, 404);

        $meta->title = sprintf('Posts about "%s" | Blog', $category->title());

        $posts = Entry::query()
            ->where('collection', 'posts')
            ->whereStatus('published')
            ->whereTaxonomy($category->id())
            ->get()
            ->sortByDesc(fn ($entry) => $entry->date())
            ->values()
            ->paginate($page)
            ->withRoute('blog.category.index', ['category' => $category->slug()]);

        return view('pages.blog.category', compact('category', 'posts'));
    }
}
