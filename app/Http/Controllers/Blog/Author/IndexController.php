<?php

namespace App\Http\Controllers\Blog\Author;

use App\Services\OgImage;
use Statamic\Facades\Entry;

class IndexController
{
    public function __invoke(OgImage $ogImage, string $author, int $page = 1)
    {
        $author = Entry::query()
            ->where('collection', 'authors')
            ->where('slug', $author)
            ->first();

        abort_unless($author, 404);

        $posts = Entry::query()
            ->where('collection', 'posts')
            ->whereStatus('published')
            ->where('author', $author->id())
            ->get()
            ->sortByDesc(fn ($entry) => $entry->date())
            ->values()
            ->paginate($page)
            ->withRoute('blog.author.index', ['author' => $author->slug()]);

        return view('pages.blog.author', [
            'author' => $author,
            'posts' => $posts,
            'title' => sprintf('Posts by %s | Blog', $author->value('title')),
            'image' => $ogImage->forCollectionMount('posts'),
        ]);
    }
}
