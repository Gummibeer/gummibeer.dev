<?php

use App\Http\Controllers\Blog\Category\FeedController as CategoryFeedController;
use App\Http\Controllers\Blog\FeedController as BlogFeedController;
use App\Http\Controllers\GetSitemapController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::name('blog.')->group(function (): void {
    Route::statamic('blog/search', 'pages.blog.search')->name('search');
    Route::get('blog.{format}', BlogFeedController::class)->whereIn('format', ['atom', 'rss'])->name('feed');
    Route::get('blog/categories/{category}.{format}', CategoryFeedController::class)->whereIn('format', ['atom', 'rss'])->name('category.feed');
});

Route::get('sitemap.xml', GetSitemapController::class)->name('sitemap.xml');

Route::get('robots.txt', static function (): Response {
    $content = collect([
        'User-agent' => '*',
        'Allow' => '/',
        null,
        'Sitemap' => route('sitemap.xml'),
    ])
        ->map(fn (?string $value, string $key): string => $value ? "{$key}: {$value}" : '')
        ->implode(PHP_EOL);

    return response($content)
        ->header('Content-Type', 'text/plain; charset=UTF-8');
})->name('robots.txt');

Route::redirect('blog/search.json', '/blog/search', 301);
Route::redirect('blog/feed.atom', '/blog.atom', 301);
Route::redirect('blog/feed.rss', '/blog.rss', 301);
Route::redirect('blog/p:2', '/blog?page=2', 301);
Route::redirect('blog/{year}', '/blog', 301)->where('year', '[0-9]{4}');
Route::redirect('blog/@gummibeer/feed.atom', '/blog.atom', 301);
Route::redirect('blog/@gummibeer/feed.rss', '/blog.rss', 301);
Route::redirect('blog/@gummibeer/p:2', '/blog?page=2', 301);
Route::redirect('blog/@gummibeer', '/blog', 301);

$legacyBlogCategories = [
    'alpinejs',
    'blade',
    'cli',
    'css',
    'geography',
    'js',
    'laravel',
    'open-source',
    'personal',
    'php',
    'postgresql',
    'recap',
    'tips+tricks',
];

Route::redirect('blog/{category}/feed.atom', '/blog/categories/{category}.atom', 301)->whereIn('category', $legacyBlogCategories);
Route::redirect('blog/{category}/feed.rss', '/blog/categories/{category}.rss', 301)->whereIn('category', $legacyBlogCategories);
Route::redirect('blog/{category}', '/blog/categories/{category}', 301)->whereIn('category', $legacyBlogCategories);
