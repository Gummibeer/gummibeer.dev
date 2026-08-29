<?php

namespace App\Providers;

use App\Macros\CollectionMixin;
use App\Macros\StrMixin;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

class MacroServiceProvider extends ServiceProvider
{
    private const MIXINS = [
        Collection::class => CollectionMixin::class,
        Str::class => StrMixin::class,
    ];

    public function boot(): void
    {
        /** @var Macroable $class */
        foreach (self::MIXINS as $class => $mixin) {
            forward_static_call([$class, 'mixin'], $this->app->make($mixin));
        }
    }
}
