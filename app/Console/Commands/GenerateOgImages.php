<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Spatie\Browsershot\Browsershot;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Facades\Entry;
use Statamic\Facades\Markdown;

class GenerateOgImages extends Command
{
    protected $signature = 'generate:og:images {--force}';

    protected $description = 'Generate all og:images for posts and static pages.';

    public function handle(): void
    {
        Entry::query()
            ->where('collection', 'posts')
            ->whereStatus('published')
            ->get()
            ->each(function (EntryContract $post): void {
                $date = $post->date();

                $this->saveImage(
                    "images/og/posts/{$date->format('Y-m-d')}.{$post->slug()}.png",
                    [
                        'title' => (string) $post->value('title'),
                        'date' => $date,
                        'readTime' => $this->readTime($post),
                    ],
                );
            });

        collect([
            'home' => 'Developer / Biker / Gamer',
            'me' => 'Developer / Biker / Gamer',
            'blog' => 'Blog',
            'portfolio' => 'Portfolio',
            'charity' => 'Charity',
            'uses' => 'Uses',
        ])->each(function (string $title, string $slug): void {
            $this->saveImage("images/og/static/{$slug}.png", ['title' => $title]);
        });
    }

    /**
     * @param  array{title: string, date?: mixed, readTime?: float}  $data
     */
    protected function saveImage(string $path, array $data): void
    {
        $path = resource_path($path);
        File::ensureDirectoryExists(dirname($path));

        if (File::exists($path) && ! $this->option('force')) {
            return;
        }

        $html = view('og.image', [
            ...$data,
            'stylesheet' => $this->stylesheet(),
            'interFont' => $this->fontDataUrl(base_path('node_modules/@fontsource-variable/inter/files/inter-latin-wght-normal.woff2')),
            'logoFont' => $this->fontDataUrl(base_path('node_modules/@fontsource/permanent-marker/files/permanent-marker-latin-400-normal.woff2')),
        ])->render();

        Browsershot::html($html)
            ->setNodeModulePath(base_path('node_modules'))
            ->windowSize(2048, 1170)
            ->waitForFunction('document.fonts.status === "loaded"')
            ->save($path);
    }

    private function stylesheet(): string
    {
        $manifestPath = public_path('build/manifest.json');

        if (! File::exists($manifestPath)) {
            throw new RuntimeException('Vite assets are missing. Run `npm run build` before generating OG images.');
        }

        /** @var array<string, array{file?: string}> $manifest */
        $manifest = json_decode(File::get($manifestPath), true, 512, JSON_THROW_ON_ERROR);
        $stylesheet = $manifest['resources/css/app.css']['file'] ?? null;

        if ($stylesheet === null) {
            throw new RuntimeException('The Vite manifest does not contain resources/css/app.css.');
        }

        $stylesheetPath = public_path('build/'.$stylesheet);

        if (! File::exists($stylesheetPath)) {
            throw new RuntimeException('The compiled Vite stylesheet is missing. Run `npm run build` before generating OG images.');
        }

        return File::get($stylesheetPath);
    }

    private function fontDataUrl(string $path): string
    {
        if (! File::exists($path)) {
            throw new RuntimeException("Font asset is missing at {$path}. Run `npm install` before generating OG images.");
        }

        return 'data:font/woff2;base64,'.base64_encode(File::get($path));
    }

    private function readTime(EntryContract $post): float
    {
        $html = Markdown::parse((string) $post->value('content'));
        $wordCount = mb_strlen(strip_tags($html)) / 5;
        $wordsPerMinute = 60 * 3;
        $minutes = ceil(($wordCount / $wordsPerMinute) * 2) / 2;

        return max(1, $minutes);
    }
}
