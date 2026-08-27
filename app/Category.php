<?php

namespace App;

use App\Repositories\CategoryRepository;
use App\Services\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class Category extends Model
{
    public function posts(): Collection
    {
        return Post::all()->filter(fn (Post $post): bool => in_array($this->slug, $post->categories));
    }

    public function getRouteKey()
    {
        return Str::kebab($this->slug);
    }

    public function getUrlAttribute(mixed $value = null): string
    {
        return route('blog.category.index', $this);
    }

    public function getTitleAttribute(mixed $value = null): string
    {
        return Str::of($this->slug)
            ->replace('+', ' & ')
            ->title();
    }

    public function __call($name, $arguments)
    {
        return app(CategoryRepository::class)->{$name}(...$arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        return app(CategoryRepository::class)->{$name}(...$arguments);
    }
}
