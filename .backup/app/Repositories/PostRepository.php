<?php

namespace App\Repositories;

use App\Post;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

/** @mixin Collection */
class PostRepository
{
    public function all(): Collection
    {
        return sheets()->collection('posts')->all()
            ->reject(fn (Post $post): bool => $post->is_draft)
            ->reject(fn (Post $post): bool => $post->date->startOfDay()->isFuture())
            ->sortByDesc('date')
            ->values();
    }

    public function latest(): ?Post
    {
        return $this->all()->first();
    }

    public function find(string $slug): Post
    {
        [$year, $slug] = explode('/', $slug);

        $post = $this->all()->first(fn (Post $post): bool => $post->date->year == $year && $post->slug == $slug);

        throw_if($post === null, (new ModelNotFoundException)->setModel(Post::class, $slug));

        return $post;
    }

    public function __call(string $method, array $arguments)
    {
        return $this->all()->$method(...$arguments);
    }
}
