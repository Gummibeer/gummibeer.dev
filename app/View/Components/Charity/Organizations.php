<?php

namespace App\View\Components\Charity;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Statamic\Entries\Entry;
use Statamic\Facades\Entry as EntryFacade;

final class Organizations extends Component
{
    public function render(): View
    {
        /** @var Collection<int, Entry> $charities */
        $charities = EntryFacade::query()
            ->where('collection', 'charities')
            ->whereStatus('published')
            ->get()
            ->sortBy(fn (Entry $entry): string => (string) $entry->value('title'))
            ->values();

        return view('components.charity.organizations', compact('charities'));
    }
}
