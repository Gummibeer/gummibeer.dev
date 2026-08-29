<?php

namespace App\Providers;

use App\Macros\StrMixin;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class MacroServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Str::mixin($this->app->make(StrMixin::class));
    }
}
