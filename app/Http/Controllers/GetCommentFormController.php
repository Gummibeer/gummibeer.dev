<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Statamic\Facades\Entry;

class GetCommentFormController
{
    public function __invoke(string $post): View
    {
        $entry = Entry::find($post);

        abort_unless($entry && $entry->collection()->handle() === 'posts', 404);

        return view('comments.form', [
            'post' => $entry,
        ]);
    }
}
