<?php

namespace App\Providers;

use App\Services\SeoMetadata;
use Honeystone\Seo\Contracts\BuildsMetadata;
use Honeystone\Seo\MetadataDirector;
use Illuminate\Support\ServiceProvider;
use Statamic\Facades\Cascade;
use Statamic\View\Cascade as ViewCascade;

class SeoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(BuildsMetadata::class, MetadataDirector::class);
    }

    public function boot(): void
    {
        Cascade::hydrated(static function (ViewCascade $cascade): void {
            app()->forgetInstance(BuildsMetadata::class);
            app(SeoMetadata::class)->configure($cascade);
        });
    }
}
