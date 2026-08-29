<?php

namespace App\View\Components\Home;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Statamic\Entries\Entry;
use Statamic\Facades\Entry as EntryFacade;

final class Streams extends Component
{
    public function render(): View
    {
        /** @var Collection<int, Entry> $streams */
        $streams = EntryFacade::query()
            ->where('collection', 'streams')
            ->whereStatus('published')
            ->get()
            ->sortByDesc(fn (Entry $entry) => $entry->date())
            ->values();

        return view('components.home.streams', compact('streams'));
    }
}
