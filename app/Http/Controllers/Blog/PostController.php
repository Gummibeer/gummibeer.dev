<?php

namespace App\Http\Controllers\Blog;

use App\Services\MetaBag;
use Carbon\Carbon;
use Statamic\Facades\Entry;

class PostController
{
    public function __invoke(MetaBag $meta, int $year, string $post)
    {
        $post = Entry::query()
            ->where('collection', 'posts')
            ->whereStatus('published')
            ->where('slug', $post)
            ->where('date', '>=', Carbon::create($year)->startOfYear())
            ->where('date', '<=', Carbon::create($year)->endOfYear())
            ->first();

        abort_unless($post, 404);

        $meta->title = $post->value('title').' | Blog';
        $meta->description = $post->value('description');
        $meta->image = asset(sprintf('images/og/posts/%s.%s.png', $post->date()->format('Y-m-d'), $post->slug()));

        return view('pages.blog.post', compact('post'));
    }
}
