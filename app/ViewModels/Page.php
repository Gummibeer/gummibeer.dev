<?php

namespace App\ViewModels;

use App\Services\MetaBag;
use Carbon\Carbon;
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
            'resume' => $this->resume($entry),
            'uses' => $this->uses($entry),
            'charity' => $this->charity($entry),
            'portfolio' => $this->portfolio($entry),
            'imprint' => $this->simplePage($entry, 'Imprint'),
            'privacy' => $this->simplePage($entry, 'Privacy'),
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

        return [
            'me' => $page,
            'posts' => $this->published('posts')->sortByDesc(fn (Entry $entry) => $entry->date())->values(),
            'streams' => $this->published('streams')->sortByDesc(fn (Entry $entry) => $entry->date())->values(),
        ];
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
    private function resume(Entry $page): array
    {
        $meta = app(MetaBag::class);
        $meta->title = 'Resume';
        $meta->image = asset('images/og/static/me.png');

        $jobs = EntryFacade::whereCollection('jobs')
            ->filter(fn (mixed $entry): bool => $entry instanceof Entry)
            ->sort(function (Entry $a, Entry $b): int {
                $aHasEnd = filled($a->value('end_at'));
                $bHasEnd = filled($b->value('end_at'));

                if ($aHasEnd === $bHasEnd) {
                    return Carbon::parse($b->value('start_at'))->timestamp <=> Carbon::parse($a->value('start_at'))->timestamp;
                }

                return $aHasEnd ? 1 : -1;
            })
            ->values();

        return [
            'contents' => $page->content,
            'jobs' => $jobs,
            'hacktoberfests' => EntryFacade::whereCollection('hacktoberfest')
                ->filter(fn (mixed $entry): bool => $entry instanceof Entry)
                ->sortByDesc(fn (Entry $entry) => $entry->slug()),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function uses(Entry $page): array
    {
        $meta = app(MetaBag::class);
        $meta->title = 'Uses';
        $meta->description = 'Software and Tools I use in my daily live for development and some little helpers to improve my experience.';
        $meta->image = asset('images/og/static/uses.png');

        return ['contents' => $page->content];
    }

    /**
     * @return array<string, mixed>
     */
    private function charity(Entry $page): array
    {
        $meta = app(MetaBag::class);
        $meta->title = 'Charity';
        $meta->description = 'For me it\'s part of my obligation and responsibility to support what I believe is important for me, us and our planet.';
        $meta->image = asset('images/og/static/charity.png');

        return [
            'contents' => $page->content,
            'charities' => $page->value('charities') ?? [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function portfolio(Entry $page): array
    {
        $meta = app(MetaBag::class);
        $meta->title = 'Portfolio';
        $meta->description = 'In my free time I support several local business owners with everything I know.';
        $meta->image = asset('images/og/static/portfolio.png');

        return [
            'contents' => $page->content,
            'projects' => $page->value('projects') ?? [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function simplePage(Entry $page, string $title): array
    {
        app(MetaBag::class)->title = $title;

        return ['contents' => $page->content];
    }

    /**
     * @return Collection<int, Entry>
     */
    private function published(string $collection): Collection
    {
        return EntryFacade::whereCollection($collection)
            ->filter(fn (mixed $entry): bool => $entry instanceof Entry && $entry->status() === 'published')
            ->values();
    }
}
