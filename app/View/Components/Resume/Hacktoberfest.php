<?php

namespace App\View\Components\Resume;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Statamic\Entries\Entry;
use Statamic\Facades\Entry as EntryFacade;

final class Hacktoberfest extends Component
{
    public function render(): View
    {
        /** @var Collection<int, Entry> $hacktoberfests */
        $hacktoberfests = EntryFacade::query()
            ->where('collection', 'hacktoberfest')
            ->whereStatus('published')
            ->get()
            ->sortByDesc(fn (Entry $entry) => $entry->slug())
            ->values();

        return view('components.resume.hacktoberfest', compact('hacktoberfests'));
    }
}
