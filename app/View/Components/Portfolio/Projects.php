<?php

namespace App\View\Components\Portfolio;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Statamic\Entries\Entry;
use Statamic\Facades\Entry as EntryFacade;

final class Projects extends Component
{
    public function render(): View
    {
        /** @var Collection<int, Entry> $projects */
        $projects = EntryFacade::query()
            ->where('collection', 'projects')
            ->whereStatus('published')
            ->get()
            ->sortBy(fn (Entry $entry): string => (string) $entry->value('title'))
            ->values();

        return view('components.portfolio.projects', compact('projects'));
    }
}
