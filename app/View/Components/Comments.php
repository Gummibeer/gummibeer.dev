<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Entries\Entry;
use Statamic\Facades\Entry as EntryFacade;

final class Comments extends Component
{
    public function __construct(public EntryContract $post) {}

    public function render(): View
    {
        /** @var Collection<int, Entry> $comments */
        $comments = EntryFacade::query()
            ->where('collection', 'comments')
            ->whereStatus('published')
            ->where('post', $this->post->id())
            ->orderBy('date', 'asc')
            ->get();

        return view('components.comments', compact('comments'));
    }
}
