<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Spatie\Emoji\Emoji;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Facades\Entry;

class PromotePost extends Command
{
    protected $name = 'post:promote';

    protected $description = 'Promote pending promotable posts.';

    public function handle(): int
    {
        $posts = Entry::query()
            ->where('collection', 'posts')
            ->whereStatus('published')
            ->get()
            ->filter(fn (EntryContract $post): bool => (bool) ($post->value('should_promote') ?? true))
            ->filter(fn (EntryContract $post): bool => blank($post->value('promoted_at')));

        if ($posts->isEmpty()) {
            $this->warn('🔎 Nothing to promote');

            return 0;
        }

        /** @var EntryContract $post */
        $post = $posts->sortBy(fn (EntryContract $post) => $post->date())->first();
        $url = route('blog.post', ['year' => $post->date()?->year, 'post' => $post->slug()]);

        $this->comment('🚀 "'.$post->value('title').'" '.$url);

        $response = Http::post(
            sprintf('https://api.telegram.org/bot%s/sendMessage', config('services.telegram.bot_token')),
            [
                'chat_id' => config('services.telegram.chat_id'),
                'text' => Emoji::orangeBook().' '.$post->value('title').PHP_EOL.$url,
            ]
        );

        if ($response->json()['ok'] ?? false) {
            $this->info('✅ promoted');

            $post->set('promoted_at', now()->toIso8601String());
            $post->save();

            return 0;
        }

        $this->error('❌ failed');
        $this->comment($response->body());

        return 1;
    }
}
