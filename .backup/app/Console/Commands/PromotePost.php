<?php

namespace App\Console\Commands;

use App\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Spatie\Emoji\Emoji;

class PromotePost extends Command
{
    protected $name = 'post:promote';

    protected $description = 'Promote pending promotable posts.';

    public function handle(): int
    {
        $posts = collect(json_decode(json_encode(simplexml_load_string(Http::get(route('sitemap.xml')))), true)['url'])
            ->pluck('loc')
            ->map(fn (string $loc): string => str_replace(url('/'), '', $loc))
            ->map(fn (string $path): string => trim($path, '/'))
            ->filter(fn (string $path): bool => Str::startsWith($path, 'blog/'))
            ->map(fn (string $path): string => str_replace('blog/', '', $path))
            ->filter(fn (string $slug): bool => Str::contains($slug, '/'))
            ->map(fn (string $slug): Post => Post::find($slug))
            ->filter(fn (Post $post): bool => $post->should_promote)
            ->filter(fn (Post $post): bool => $post->promoted_at === null);

        if ($posts->isEmpty()) {
            $this->warn('🔎 Nothing to promote');

            return 0;
        }

        /** @var Post $post */
        $post = $posts->sortBy('date')->first();

        $this->comment('🚀 "'.$post->title.'" '.$post->url);

        $response = Http::post(
            sprintf('https://api.telegram.org/bot%s/sendMessage', config('services.telegram.bot_token')),
            [
                'chat_id' => config('services.telegram.chat_id'),
                'text' => Emoji::orangeBook().' '.$post->title.PHP_EOL.$post->url,
            ]
        );

        if ($response->json()['ok'] ?? false) {
            $this->info('✅ promoted');

            $post->promoted_at = now();
            $post->save();

            return 0;
        }

        $this->error('❌ failed');
        $this->comment($response->body());

        return 1;
    }
}
