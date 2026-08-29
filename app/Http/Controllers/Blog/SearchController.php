<?php

namespace App\Http\Controllers\Blog;

use App\Services\OgImage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SearchController
{
    public function __invoke(Request $request, OgImage $ogImage): View
    {
        $query = trim((string) $request->query('q'));

        return view('pages.blog.search', [
            'title' => $query === '' ? 'Search' : "Search: {$query}",
            'image' => $ogImage->forCollectionMount('posts'),
        ]);
    }
}
