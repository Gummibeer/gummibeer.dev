<?php

namespace App\Console\Commands;

use DG\Twitter\Twitter;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use stdClass;

class TwitterGiveaway extends Command
{
    protected $signature = 'twitter:giveaway {id} {--count=1} {--exclude=*} {--add=*} {--live} {--winner=*}';

    protected $description = 'Pick a Twitter retweet giveaway winner.';

    protected Twitter $twitter;

    public function handle(Twitter $twitter): void
    {
        $this->twitter = $twitter;

        $count = $this->option('count');
        $id = $this->argument('id');

        $tweet = $twitter->request(sprintf('statuses/show/%s.json?trim_user=true', $id), 'GET')->text;

        $excluded = collect($this->option('exclude'))
            ->push('devgummibeer', 'astrotomic_oss')
            ->map(fn (string $name): string => str_replace('@', '', $name));

        $added = collect($this->option('add'))
            ->map(fn (string $name): string => str_replace('@', '', $name));

        $this->warn('🎁 Twitter Giveaway Picker');
        $this->line($tweet);
        $this->info(sprintf('» pick %d winners for %s tweet', $count, $id));
        $this->info(sprintf('» add to pool: %s', $added->implode(', ')));
        $this->info(sprintf('» exclude from pool: %s', $excluded->implode(', ')));

        $ids = collect();
        $cursor = -1;
        do {
            $response = $twitter->request(
                sprintf('statuses/retweeters/ids.json?id=%s&count=100&stringify_ids=true&cursor=%d', $id, $cursor),
                'GET'
            );
            $ids->push(...$response->ids);
            $cursor = $response->next_cursor;
        } while ($cursor != 0);

        $users = $ids
            ->chunk(100)
            ->map(fn (Collection $ids): array => $twitter->request(
                sprintf('users/lookup.json?user_id=%s', $ids->implode(',')),
                'GET'
            ))
            ->collapse()
            ->map(fn (stdClass $user): string => $user->screen_name)
            ->push(...$added)
            ->reject(fn (string $username): bool => $excluded->contains($username))
            ->values();

        $this->info(sprintf('» found %d eligible users ', $users->count()));

        $winners = collect($this->option('winner'))
            ->map(fn (string $username): array => $this->getUser($username));
        do {
            $winners = $winners->merge(
                $users->random($count)
                    ->map(fn (string $username): array => $this->getUser($username))
                    ->reject(fn (array $user): bool => Str::contains($user['avatar'], 'default'))
            )->unique('username')->take($count);
        } while ($winners->count() < $count);

        $winners->each(function (array $user): void {
            $html = <<<HTML
                <div style="font-size:8rem;">
                    <div class="inline-block font-logo text-center text-brand bg-white" style="font-size:10rem;margin-bottom:0.5em;padding:0 0.25em;border-radius:50px;">Congratulations</div>
                    <img src="{$user['avatar']}" style="margin-left:auto;margin-right:auto;margin-bottom:0.125em;width:512px;height:512px;border-radius:50%;object-fit:cover;border:#ffffff solid 0.25em;"/>
                    <h1 class="inline-block text-black text-center bg-white" style="margin-bottom:0.5em;padding:0 0.25em;border-radius:50px;">{$user['name']}</h1>
                </div>
            HTML;

            $this->saveImage("images/giveaway/{$user['username']}.png", $html);
        });

        if ($this->option('live')) {
            $delay = 10;
            $interval = 100;
            $start = microtime(true);

            $this->line('');
            while (microtime(true) < $start + $delay) {
                $this->output->write("\33[2K\r");
                $this->output->write(sprintf(
                    '🎰 %s',
                    number_format($start + $delay - microtime(true), 1, '.', '')
                ));
                usleep($interval);
            }
            $this->output->write("\33[2K\r");
        }

        $this->warn(sprintf('🎉 %s 🎉', $winners->implode('username', ', ')));
    }

    protected function saveImage(string $path, string $html): void
    {
        $url = 'https://gummibeer-og-image.vercel.app/'.rawurlencode(trim($html)).'.png?confetti=true';

        @mkdir(dirname(resource_path($path)), 0755, true);
        file_put_contents(resource_path($path), file_get_contents($url));
    }

    protected function getUser(string $username): array
    {
        $user = $this->twitter->request(sprintf('https://api.twitter.com/1.1/users/show.json?screen_name=%s&include_entities=false', $username), 'GET');

        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->screen_name,
            'avatar' => str_replace('_normal', '', $user->profile_image_url_https),
        ];
    }
}
