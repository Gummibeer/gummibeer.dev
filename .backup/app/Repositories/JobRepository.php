<?php

namespace App\Repositories;

use App\Job;
use Illuminate\Support\Collection;

/** @mixin Collection */
class JobRepository
{
    public function all(): Collection
    {
        return sheets('jobs')->all()
            ->sort(function (Job $a, Job $b): int {
                if (count(array_unique([$a->hasEnd(), $b->hasEnd()])) === 1) {
                    return $a->start_at->isBefore($b->start_at) ? 1 : -1;
                }

                return $a->hasEnd() ? 1 : -1;
            });
    }
}
