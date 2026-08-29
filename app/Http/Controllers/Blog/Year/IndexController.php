<?php

namespace App\Http\Controllers\Blog\Year;

use App\Services\OgImage;
use Carbon\Carbon;
use Statamic\Facades\Entry;

class IndexController
{
    public function __invoke(OgImage $ogImage, int $year, int $page = 1)
    {
        $posts = Entry::query()
            ->where('collection', 'posts')
            ->whereStatus('published')
            ->where('date', '>=', Carbon::create($year)->startOfYear())
            ->where('date', '<=', Carbon::create($year)->endOfYear())
            ->get()
            ->sortByDesc(fn ($entry) => $entry->date())
            ->values()
            ->paginate($page)
            ->withRoute('blog.year.index', compact('year'));

        return view('pages.blog.year', [
            'year' => $year,
            'posts' => $posts,
            'title' => sprintf('Posts from %d | Blog', $year),
            'image' => $ogImage->forCollectionMount('posts'),
        ]);
    }
}
