<?php

namespace App\ViewModels;

use Statamic\Entries\Entry;
use Statamic\View\ViewModel;

class Post extends ViewModel
{
    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        $post = $this->cascade->get('page');

        if (! $post instanceof Entry) {
            return [];
        }

        return compact('post');
    }
}
