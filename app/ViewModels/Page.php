<?php

namespace App\ViewModels;

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
            'resume' => $this->resume(),
            'uses' => $this->simplePage(),
            'charity' => $this->charity(),
            'portfolio' => $this->portfolio(),
            'imprint', 'privacy' => $this->simplePage(),
            default => [],
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function home(Entry $page): array
    {
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
            'contents' => $this->cascade->get('content'),
            'jobs' => $jobs,
            'hacktoberfests' => EntryFacade::whereCollection('hacktoberfest')
                ->filter(fn (mixed $entry): bool => $entry instanceof Entry)
                ->sortByDesc(fn (Entry $entry) => $entry->slug()),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function charity(): array
    {
        return [
            'contents' => $this->cascade->get('content'),
            'charities' => $this->published('charities')
                ->sortBy(fn (Entry $entry): string => (string) $entry->value('title'))
                ->values(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function portfolio(): array
    {
        return [
            'contents' => $this->cascade->get('content'),
            'projects' => $this->published('projects')
                ->sortBy(fn (Entry $entry): string => (string) $entry->value('title'))
                ->values(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function simplePage(): array
    {
        return ['contents' => $this->cascade->get('content')];
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
