<?php

use App\Http\Controllers\Blog\Category\FeedController as CategoryFeedController;
use App\Http\Controllers\Blog\FeedController as BlogFeedController;
use App\Http\Controllers\GetMarkdownController;
use App\Http\Controllers\GetSitemapController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::name('blog.')->group(function (): void {
    Route::statamic('blog/search', 'pages.blog.search')->name('search');
    Route::get('blog.{format}', BlogFeedController::class)->whereIn('format', ['atom', 'rss'])->name('feed');
    Route::get('blog/categories/{category}.{format}', CategoryFeedController::class)->whereIn('format', ['atom', 'rss'])->name('category.feed');
});

Route::get('{uri}.md', GetMarkdownController::class)
    ->where('uri', '.*')
    ->name('markdown');
Route::get('sitemap.xml', GetSitemapController::class)->name('sitemap.xml');

Route::get('robots.txt', static function (): Response {
    $content = implode(PHP_EOL, [
        'User-agent: *',
        'Allow: /',
        '',
        'User-agent: GPTBot',
        'Allow: /',
        '',
        'User-agent: ClaudeBot',
        'Allow: /',
        '',
        'User-agent: PerplexityBot',
        'Allow: /',
        '',
        'User-agent: OAI-SearchBot',
        'Allow: /',
        '',
        'User-agent: CCBot',
        'Disallow: /',
        '',
        'User-agent: ByteSpider',
        'Disallow: /',
        '',
        'Content-Signal: search=yes, ai-train=no',
        '',
        'Sitemap: '.route('sitemap.xml'),
    ]);

    return response($content)
        ->header('Content-Type', 'text/plain; charset=UTF-8');
})->name('robots.txt');
