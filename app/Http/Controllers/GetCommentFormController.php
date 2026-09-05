<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Statamic\Facades\Entry;

class GetCommentFormController
{
    public function __invoke(string $post): View
    {
        $entry = Entry::query()
            ->where('collection', 'posts')
            ->where('id', $post)
            ->firstOrFail();

        return view('comments.form', [
            'post' => $entry,
        ]);
    }
}
