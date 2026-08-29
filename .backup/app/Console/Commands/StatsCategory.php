<?php

namespace App\Console\Commands;

use App\Category;
use Illuminate\Console\Command;

class StatsCategory extends Command
{
    protected $name = 'stats:category';

    protected $description = 'Show category statistics.';

    public function handle()
    {
        $this->table(
            [
                'slug' => '#',
                'title' => 'Title',
                'post_count' => 'Posts',
            ],
            Category::all()->map(fn (Category $category): array => [
                'slug' => $category->slug,
                'title' => $category->title,
                'post_count' => $category->posts()->count(),
            ])->sortByDesc('post_count')
        );
    }
}
