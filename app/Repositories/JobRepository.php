<?php

namespace App\Repositories;

use App\Job;
use Illuminate\Support\Collection;
use Statamic\Facades\Entry;

/** @mixin Collection */
class JobRepository
{
    public function all(): Collection
    {
        return Entry::query()
            ->where('collection', 'jobs')
            ->get()
            ->map(fn ($entry): Job => new Job($entry))
            ->sort(function (Job $a, Job $b): int {
                if (count(array_unique([$a->hasEnd(), $b->hasEnd()])) === 1) {
                    return $a->start_at->isBefore($b->start_at) ? 1 : -1;
                }

                return $a->hasEnd() ? 1 : -1;
            })
            ->values();
    }
}
