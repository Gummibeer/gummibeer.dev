<?php

namespace App\Http\Controllers\Blog\Author;

use App\Services\MetaBag;
use Illuminate\Contracts\View\View;
use Statamic\Facades\Entry;

class IndexController
{
    public function __invoke(MetaBag $meta, string $author): View
    {
        $author = Entry::query()
            ->where('collection', 'authors')
            ->whereStatus('published')
            ->where('slug', $author)
            ->first();

        abort_unless($author, 404);

        $meta->title = sprintf('Posts by %s | Blog', $author->value('title'));

        return view('pages.blog.author', compact('author'));
    }
}
