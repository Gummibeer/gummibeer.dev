<?php

namespace App\Repositories;

use App\Content;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Statamic\Facades\Entry;

class ContentRepository
{
    public function all(string $collection = 'static'): Collection
    {
        return Entry::query()
            ->where('collection', $collection)
            ->get()
            ->map(fn ($entry): Content => new Content($entry))
            ->values();
    }

    public function find(string $slug, string $collection = 'static'): Content
    {
        $content = $this->all($collection)
            ->first(fn (Content $content): bool => $content->slug === $slug);

        throw_if(
            $content === null,
            (new ModelNotFoundException)->setModel(Content::class, $slug)
        );

        return $content;
    }
}
