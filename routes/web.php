<?php

use App\Http\Controllers\Blog\Category\FeedController as CategoryFeedController;
use App\Http\Controllers\Blog\FeedController as BlogFeedController;
use App\Http\Controllers\GetMarkdownController;
use App\Http\Controllers\GetSitemapController;
use App\Http\Controllers\Streams\FeedController as StreamsFeedController;
use Illuminate\Support\Facades\Route;

Route::name('blog.')->group(function (): void {
    Route::statamic('blog/search', 'pages.blog.search')->name('search');
    Route::get('blog.{format}', BlogFeedController::class)->whereIn('format', ['atom', 'rss'])->name('feed');
    Route::get('blog/categories/{category}.{format}', CategoryFeedController::class)->whereIn('format', ['atom', 'rss'])->name('category.feed');
});

Route::name('streams.')->group(function (): void {
    Route::get('streams.{format}', StreamsFeedController::class)->whereIn('format', ['atom', 'rss'])->name('feed');
});

Route::get('{uri}.md', GetMarkdownController::class)
    ->where('uri', '.*')
    ->name('markdown');
Route::get('sitemap.xml', GetSitemapController::class)->name('sitemap.xml');
