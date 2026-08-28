<?php

namespace App\Http\Controllers\Blog;

use App\Services\MetaBag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SearchController
{
    public function __invoke(Request $request, MetaBag $meta): View
    {
        $query = trim((string) $request->query('q'));

        $meta->title = $query === '' ? 'Search' : "Search: {$query}";
        $meta->image = asset('images/og/static/blog.png');

        return view('pages.blog.search');
    }
}
