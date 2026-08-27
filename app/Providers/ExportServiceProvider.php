<?php

namespace App\Providers;

use App\Author;
use App\Category;
use App\Post;
use App\Stream;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Statamic\StaticSite\SSG;

class ExportServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        SSG::addUrls(fn (): array => $this->urls()->all());
    }

    private function urls(): Collection
    {
        $urls = collect([
            '/blog/feed.rss',
            '/blog/feed.atom',
        ]);

        $posts = Post::all();
        $streams = Stream::all();
        $blogItems = $posts->merge($streams)->sortByDesc('date')->values();

        $urls->push(...$this->paginationUrls('/blog', $blogItems->count()));

        $posts
            ->groupBy(fn (Post $post): int => $post->date->year)
            ->each(function (Collection $yearPosts, int $year) use ($urls): void {
                $urls->push("/blog/{$year}");
                $urls->push(...$this->paginationUrls("/blog/{$year}", $yearPosts->count()));
            });

        $posts->each(fn (Post $post) => $urls->push('/blog/'.$post->getRouteKey()));

        Category::all()->each(function (Category $category) use ($urls): void {
            $base = '/blog/'.$category->getRouteKey();

            $urls->push(
                $base,
                $base.'/feed.rss',
                $base.'/feed.atom',
                ...$this->paginationUrls($base, $category->posts()->count())
            );
        });

        Author::all()->each(function (Author $author) use ($urls): void {
            $base = '/blog/@'.$author->getRouteKey();

            $urls->push(
                $base,
                $base.'/feed.rss',
                $base.'/feed.atom',
                ...$this->paginationUrls($base, $author->posts()->count())
            );
        });

        return $urls->filter()->unique()->values();
    }

    private function paginationUrls(string $base, int $count, int $perPage = 6): array
    {
        if ($count <= $perPage) {
            return [];
        }

        return collect(range(2, (int) ceil($count / $perPage)))
            ->map(fn (int $page): string => "{$base}/p:{$page}")
            ->all();
    }
}
