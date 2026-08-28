<?php

namespace App\ViewModels;

use App\Services\MetaBag;
use Statamic\Entries\Entry;
use Statamic\Taxonomies\LocalizedTerm;
use Statamic\View\ViewModel;

class Category extends ViewModel
{
    public function data(): array
    {
        $category = $this->cascade->get('page');

        if (! $category instanceof LocalizedTerm) {
            return [];
        }

        app(MetaBag::class)->title = sprintf('Posts about "%s" | Blog', $category->title());

        $posts = $category->entries()
            ->filter(fn (mixed $entry): bool => $entry instanceof Entry && $entry->status() === 'published')
            ->sortByDesc(fn (Entry $entry) => $entry->date())
            ->values()
            ->paginate()
            ->withRoute('blog.category.index', ['category' => $category->slug()], $category->url());

        return compact('category', 'posts');
    }
}
