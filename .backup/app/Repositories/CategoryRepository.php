<?php

namespace App\Repositories;

use App\Category;
use App\Post;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CategoryRepository
{
    protected PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function all(): Collection
    {
        return $this->postRepository->all()
            ->map(fn (Post $post): Collection => $post->categories())
            ->collapse()
            ->unique('slug');
    }

    public function find(string $slug): Category
    {
        return $this->all()
            ->first(fn (Category $category): bool => Str::kebab($category->slug) === Str::kebab($slug));
    }
}
