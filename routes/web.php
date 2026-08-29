<?php

use App\Http\Controllers\Blog;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

Route::prefix('blog')->name('blog.')->group(function (): void {
    Route::statamic('search', 'pages.blog.search')->name('search');
    Route::get('feed.{format}', Blog\FeedController::class)->name('feed');
    Route::get('categories/{category}/feed.{format}', Blog\Category\FeedController::class)->name('category.feed');
});

Route::get(
    'sitemap.xml',
    fn () => SitemapGenerator::create(url('/'))
        ->hasCrawled(function (Url $url): Url {
            $url->setUrl(rtrim($url->url, '/'));

            if (in_array($url->segment(1), ['resume', 'portfolio', 'charity', 'uses'])) {
                $url
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.5);
            }

            if (in_array($url->segment(1), ['imprint', 'privacy'])) {
                $url
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                    ->setPriority(0.1);
            }

            if ($url->segment(1) === 'blog') {
                $url
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                    ->setPriority(1);
            }

            return $url;
        })
        ->getSitemap()
)->name('sitemap.xml');

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
