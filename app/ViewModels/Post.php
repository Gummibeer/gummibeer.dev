<?php

namespace App\ViewModels;

use App\Services\MetaBag;
use Statamic\Entries\Entry;
use Statamic\View\ViewModel;

class Post extends ViewModel
{
    public function data(): array
    {
        $post = $this->cascade->get('page');

        if (! $post instanceof Entry) {
            return [];
        }

        $meta = app(MetaBag::class);
        $meta->title = $post->value('title').' | Blog';
        $meta->description = $post->value('description');

        if ($date = $post->date()) {
            $meta->image = asset(sprintf('images/og/posts/%s.%s.png', $date->format('Y-m-d'), $post->slug()));
        }

        return compact('post');
    }
}
