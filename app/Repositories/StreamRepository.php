<?php

namespace App\Repositories;

use App\Stream;
use Illuminate\Support\Collection;
use Statamic\Facades\Entry;

/** @mixin Collection */
class StreamRepository
{
    public function all(): Collection
    {
        return Entry::query()
            ->where('collection', 'streams')
            ->get()
            ->map(fn ($entry): Stream => new Stream($entry))
            ->sortByDesc('date')
            ->values();
    }

    public function latest(): ?Stream
    {
        return $this->all()->first();
    }

    public function __call(string $method, array $arguments)
    {
        return $this->all()->{$method}(...$arguments);
    }
}
