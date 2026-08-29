<?php

namespace App\Providers;

use DG\Twitter\Twitter;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class TwitterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Twitter::class, function (): Twitter {
            return new Twitter(
                config('services.twitter.consumer_key'),
                config('services.twitter.consumer_secret'),
                config('services.twitter.access_token'),
                config('services.twitter.access_token_secret')
            );
        });
    }

    public function boot(): void
    {
        File::macro('json', function (string $path, bool $lock = false) {
            return json_decode(File::get($path, $lock), true);
        });

        if (File::exists(base_path('.twitter.json'))) {
            $this->app->make(ConfigContract::class)->set(
                'services.twitter',
                File::json(base_path('.twitter.json'))
            );
        }
    }
}
