<?php

use App\Http\Controllers\Blog;
use App\Http\Middleware\Paginated;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::prefix('blog')->name('blog.')->group(function (): void {
    Route::get('search', Blog\SearchController::class)->name('search');
    Route::get('feed.{format}', Blog\FeedController::class)->name('feed');
    Route::get('{page}', Blog\IndexController::class)->middleware(Paginated::class)->name('index');

    Route::get('{year}/{page?}', Blog\Year\IndexController::class)->middleware(Paginated::class)->whereNumber('year')->name('year.index');

    Route::prefix('@{author}')->name('author.')->group(function (): void {
        Route::get('{page?}', Blog\Author\IndexController::class)->middleware(Paginated::class)->name('index');
        Route::get('feed.{format}', Blog\Author\FeedController::class)->name('feed');
    });

    Route::prefix('{category}')->name('category.')->group(function (): void {
        Route::get('{page?}', Blog\Category\IndexController::class)->middleware(Paginated::class)->name('index');
        Route::get('feed.{format}', Blog\Category\FeedController::class)->name('feed');
    });
});

Route::get(
    '404.html',
    fn () => response()->view('pages.404', ['response_code' => 404], 404)
)->name('404');

Route::get('robots.txt', static function (): Response {
    $content = collect([
        'User-agent' => '*',
        'Allow' => '/',
        null,
        'Sitemap' => url('/sitemap.xml'),
    ])
        ->map(fn (?string $value, string $key): string => $value ? "{$key}: {$value}" : '')
        ->implode(PHP_EOL);

    return response($content)
        ->header('Content-Type', 'text/plain; charset=UTF-8');
})->name('robots.txt');
