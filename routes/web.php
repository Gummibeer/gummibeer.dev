<?php

use App\Http\Controllers\GetMarkdownController;
use App\Http\Controllers\GetSitemapController;
use App\Http\Controllers\Journal\FeedController as JournalFeedController;
use Illuminate\Support\Facades\Route;

Route::statamic('blog/search', 'pages.blog.search')->name('blog.search');
Route::get('journal.{format}', JournalFeedController::class)
    ->whereIn('format', ['atom', 'rss'])
    ->name('journal.feed');

Route::get('{uri}.md', GetMarkdownController::class)
    ->where('uri', '.*')
    ->name('markdown');
Route::get('sitemap.xml', GetSitemapController::class)->name('sitemap.xml');
