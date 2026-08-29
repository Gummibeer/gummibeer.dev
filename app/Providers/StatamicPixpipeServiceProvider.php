<?php

namespace App\Providers;

use App\Imaging\PixpipeGlideManager;
use Illuminate\Support\ServiceProvider;
use Statamic\Imaging\GlideManager;
use Statamic\Providers\GlideServiceProvider;

class StatamicPixpipeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GlideManager::class, PixpipeGlideManager::class);

        // Statamic defers its imaging bindings. Register them after replacing
        // the manager so every standard Glide server is Pixpipe-backed.
        $this->app->register(GlideServiceProvider::class);
    }
}
