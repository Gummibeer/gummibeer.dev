<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Statamic\Contracts\Taxonomies\Term as TermContract;
use Statamic\Facades\Entry;
use Statamic\Facades\Term;

class StatsCategory extends Command
{
    protected $name = 'stats:category';

    protected $description = 'Show category statistics.';

    public function handle(): int
    {
        $this->table(
            [
                'slug' => '#',
                'title' => 'Title',
                'post_count' => 'Posts',
            ],
            Term::whereTaxonomy('categories')
                ->map(fn (TermContract $category): array => [
                    'slug' => $category->slug(),
                    'title' => $category->title(),
                    'post_count' => Entry::query()
                        ->where('collection', 'posts')
                        ->whereStatus('published')
                        ->whereTaxonomy($category->id())
                        ->get()
                        ->count(),
                ])
                ->sortByDesc('post_count')
        );

        return self::SUCCESS;
    }
}
