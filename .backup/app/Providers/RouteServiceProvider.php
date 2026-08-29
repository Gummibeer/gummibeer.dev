<?php

namespace App\Providers;

use Closure;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/';

    public function boot(): void
    {
        Route::pattern('year', '[0-9]{4}');
        Route::pattern('page', 'p:([0-9]+)');
        Route::pattern('post', '[0-9]{4}\/[a-z0-9\-]+');

        Route::macro('sheet', function (string $uri, string $view, string $sheet, Closure $callback) {
            return Route::get($uri, function () use ($view, $sheet, $callback): View {
                $data = sheets()->get($sheet);

                app()->call($callback, ['data' => $data]);

                return view($view, $data->toArray());
            });
        });

        parent::boot();
    }

    public function map(): void
    {
        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    }
}
