<?php

namespace App\Http\Controllers\Blog\Year;

use App\Services\MetaBag;
use Illuminate\Contracts\View\View;

class IndexController
{
    public function __invoke(MetaBag $meta, int $year): View
    {
        $meta->title = sprintf('Posts from %d | Blog', $year);

        return view('pages.blog.year', compact('year'));
    }
}
