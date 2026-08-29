<?php

namespace App;

use App\Repositories\CategoryRepository;
use App\Services\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @property-read string $title
 * @property-read string $slug
 * @property-read string $url
 *
 * @method static Collection|Category[] all()
 * @method static Category find(string $slug)
 */
final class Category extends Model
{
    /**
     * @return Collection|Post[]
     */
    public function posts(): Collection
    {
        return Post::all()->filter(fn (Post $post): bool => in_array($this->slug, $post->categories));
    }

    public function getRouteKey()
    {
        return Str::kebab($this->slug);
    }

    public function getUrlAttribute(): string
    {
        return route('blog.category.index', $this);
    }

    public function getTitleAttribute(): string
    {
        return Str::of($this->slug)
            ->replace('+', ' & ')
            ->title();
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([app(CategoryRepository::class), $name], $arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([app(CategoryRepository::class), $name], $arguments);
    }
}
