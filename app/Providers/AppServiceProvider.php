<?php

namespace App\Providers;

use App\Modifiers\Twemoji;
use App\Tags\Ecologi as EcologiTag;
use App\Tags\FontAwesome;
use App\Tags\GithubSponsors;
use App\Tags\OpenGraph as OpenGraphTag;
use App\Tags\Unavatar;
use Astrotomic\Ecologi\Ecologi;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            Ecologi::class,
            fn () => new Ecologi((string) config('services.ecologi.token')),
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Twemoji::register();
        EcologiTag::register();
        FontAwesome::register();
        GithubSponsors::register();
        OpenGraphTag::register();
        Unavatar::register();
    }
}
