<?php

namespace App\Repositories;

use App\Stream;
use Illuminate\Support\Collection;

/** @mixin Collection */
class StreamRepository
{
    public function all(): Collection
    {
        return sheets('streams')->all()
            ->sortByDesc('date')
            ->values();
    }

    public function latest(): ?Stream
    {
        return $this->all()->first();
    }

    public function __call(string $method, array $arguments)
    {
        return $this->all()->$method(...$arguments);
    }
}
