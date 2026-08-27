<?php

namespace App\Repositories;

use App\Post;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Statamic\Facades\Entry;

/** @mixin Collection */
class PostRepository
{
    public function all(): Collection
    {
        return Entry::query()
            ->where('collection', 'posts')
            ->get()
            ->map(fn ($entry): Post => new Post($entry))
            ->reject(fn (Post $post): bool => $post->is_draft)
            ->reject(fn (Post $post): bool => $post->date->copy()->startOfDay()->isFuture())
            ->sortByDesc('date')
            ->values();
    }

    public function latest(): ?Post
    {
        return $this->all()->first();
    }

    public function find(string $slug): Post
    {
        [$year, $slug] = array_pad(explode('/', $slug, 2), 2, null);

        $post = $this->all()->first(
            fn (Post $post): bool => (string) $post->date->year === (string) $year && $post->slug === $slug
        );

        throw_if($post === null, (new ModelNotFoundException)->setModel(Post::class, $slug));

        return $post;
    }

    public function __call(string $method, array $arguments)
    {
        return $this->all()->{$method}(...$arguments);
    }
}
