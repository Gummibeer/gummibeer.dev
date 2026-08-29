<?php

namespace App\Console\Commands;

use App\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class GenerateOgImages extends Command
{
    protected $signature = 'generate:og:images {--force}';

    protected $description = 'Generate all og:images for posts and static pages.';

    public function handle(): void
    {
        Post::all()->each(function (Post $post): void {
            $html = <<<HTML
                <div style="font-size:8rem;">
                    <div class="font-logo text-center text-brand" style="font-size:10rem;margin-bottom: 0.75em;">Tom Herrmann</div>
                    <h1 class="text-black text-center" style="margin-bottom: 0.5em;">{$post->title}</h1>
                    <div class="text-snow-20 text-sm" style="font-size:0.5em;">
                        <ul class="flex flex-row list-none" style="justify-content:center;">
                            <li style="margin-right: 1em;">
                                <i class="fal fa-fw fa-calendar" style="margin-right: 0.25em;"></i>
                                {$post->date->format('M jS, Y')}
                            </li>
                            <li>
                                <i class="fal fa-fw fa-clock" style="margin-right: 0.25em;"></i>
                                {$post->read_time} min read
                            </li>
                        </ul>
                    </div>
                </div>
            HTML;

            $this->saveImage("images/og/posts/{$post->date->format('Y-m-d')}.{$post->slug}.png", $html);
        });

        collect([
            'home' => 'Developer / Biker / Gamer',
            'me' => 'Developer / Biker / Gamer',
            'blog' => 'Blog',
            'portfolio' => 'Portfolio',
            'charity' => 'Charity',
            'uses' => 'Uses',
        ])->each(function (string $title, string $slug): void {
            $html = <<<HTML
                <div style="font-size:8rem;">
                    <div class="font-logo text-center text-brand" style="font-size:10rem;margin-bottom: 0.75em;">Tom Herrmann</div>
                    <h1 class="text-black text-center">{$title}</h1>
                </div>
            HTML;

            $this->saveImage("images/og/static/{$slug}.png", $html);
        });
    }

    protected function saveImage(string $path, string $html): void
    {
        $path = resource_path($path);
        File::ensureDirectoryExists(dirname($path));

        if(!File::exists($path) || $this->option('force')) {
            $process = new Process([
                (new ExecutableFinder)->find('node'),
                base_path('.og/index.js'),
                '--path=' . $path,
                trim($html),
            ]);
            $process->mustRun();
        }
    }
}
