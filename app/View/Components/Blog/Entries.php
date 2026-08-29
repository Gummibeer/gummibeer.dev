<?php

namespace App\View\Components\Blog;

use App\Services\Paginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Statamic\Entries\Entry;
use Statamic\Facades\Entry as EntryFacade;

class Entries extends Component
{
    public function render(): View
    {
        return view('components.blog.entries', [
            'posts' => $this->posts(),
        ]);
    }

    private function posts(): Paginator
    {
        return $this->published('posts')
            ->merge($this->published('streams'))
            ->sortByDesc(fn (Entry $entry) => $entry->date())
            ->values()
            ->paginate()
            ->withRoute('blog.index', [], '/blog');
    }

    /**
     * @return Collection<int, Entry>
     */
    private function published(string $collection): Collection
    {
        return EntryFacade::query()
            ->where('collection', $collection)
            ->whereStatus('published')
            ->get()
            ->filter(fn (mixed $entry): bool => $entry instanceof Entry)
            ->values();
    }
}
