<?php

namespace App\Http\Controllers\Blog;

use App\Post;
use App\Services\MetaBag;

class PostController
{
    public function __invoke(MetaBag $meta, Post $post)
    {
        $meta->title = $post->title.' | Blog';
        $meta->description = $post->description;
        $meta->image = mix("images/og/posts/{$post->date->format('Y-m-d')}.{$post->slug}.png");

        return view('pages.blog.post', compact('post'));
    }
}
