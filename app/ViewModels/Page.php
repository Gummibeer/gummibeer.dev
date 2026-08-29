<?php

namespace App\ViewModels;

use App\Services\MetaBag;
use Illuminate\Support\Collection;
use Statamic\Entries\Entry;
use Statamic\Facades\Entry as EntryFacade;
use Statamic\Structures\Page as StructurePage;
use Statamic\View\ViewModel;

class Page extends ViewModel
{
    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        $page = $this->cascade->get('page');

        if (! $page instanceof StructurePage || ! $page->entry() instanceof Entry) {
            return [];
        }

        $entry = $page->entry();

        return match ($entry->slug()) {
            'me' => $this->home($entry),
            'blog' => $this->blog(),
            'resume' => $this->resume(),
            'uses' => $this->uses(),
            'charity' => $this->charity(),
            'portfolio' => $this->portfolio(),
            'imprint' => $this->simplePage('Imprint'),
            'privacy' => $this->simplePage('Privacy'),
            default => [],
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function home(Entry $page): array
    {
        $meta = app(MetaBag::class);
        $meta->description = 'I\'m an enthusiastic web developer and free time gamer from Hamburg, Germany.';
        $meta->image = asset('images/og/static/home.png');

        return ['me' => $page];
    }

    /**
     * @return array<string, mixed>
     */
    private function blog(): array
    {
        $meta = app(MetaBag::class);
        $meta->title = 'Blog';
        $meta->image = asset('images/og/static/blog.png');

        $posts = $this->published('posts')
            ->merge($this->published('streams'))
            ->sortByDesc(fn (Entry $entry) => $entry->date())
            ->values()
            ->paginate()
            ->withRoute('blog.index', [], '/blog');

        return compact('posts');
    }

    /**
     * @return array<string, mixed>
     */
    private function resume(): array
    {
        $meta = app(MetaBag::class);
        $meta->title = 'Resume';
        $meta->image = asset('images/og/static/me.png');

        return ['contents' => $this->cascade->get('content')];
    }

    /**
     * @return array<string, mixed>
     */
    private function uses(): array
    {
        $meta = app(MetaBag::class);
        $meta->title = 'Uses';
        $meta->description = 'Software and Tools I use in my daily live for development and some little helpers to improve my experience.';
        $meta->image = asset('images/og/static/uses.png');

        return ['contents' => $this->cascade->get('content')];
    }

    /**
     * @return array<string, mixed>
     */
    private function charity(): array
    {
        $meta = app(MetaBag::class);
        $meta->title = 'Charity';
        $meta->description = 'For me it\'s part of my obligation and responsibility to support what I believe is important for me, us and our planet.';
        $meta->image = asset('images/og/static/charity.png');

        return ['contents' => $this->cascade->get('content')];
    }

    /**
     * @return array<string, mixed>
     */
    private function portfolio(): array
    {
        $meta = app(MetaBag::class);
        $meta->title = 'Portfolio';
        $meta->description = 'In my free time I support several local business owners with everything I know.';
        $meta->image = asset('images/og/static/portfolio.png');

        return ['contents' => $this->cascade->get('content')];
    }

    /**
     * @return array<string, mixed>
     */
    private function simplePage(string $title): array
    {
        app(MetaBag::class)->title = $title;

        return ['contents' => $this->cascade->get('content')];
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
