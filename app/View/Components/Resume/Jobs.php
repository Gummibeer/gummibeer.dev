<?php

namespace App\View\Components\Resume;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Statamic\Entries\Entry;
use Statamic\Facades\Entry as EntryFacade;

final class Jobs extends Component
{
    public function render(): View
    {
        /** @var Collection<int, Entry> $jobs */
        $jobs = EntryFacade::query()
            ->where('collection', 'jobs')
            ->whereStatus('published')
            ->get()
            ->sort(function (Entry $a, Entry $b): int {
                $aHasEnd = filled($a->value('end_at'));
                $bHasEnd = filled($b->value('end_at'));

                if ($aHasEnd === $bHasEnd) {
                    return Carbon::parse($b->value('start_at'))->timestamp <=> Carbon::parse($a->value('start_at'))->timestamp;
                }

                return $aHasEnd ? 1 : -1;
            })
            ->values();

        return view('components.resume.jobs', compact('jobs'));
    }
}
