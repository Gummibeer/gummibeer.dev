<?php

use App\Http\Controllers\Blog;
use App\Http\Middleware\Paginated;
use App\Job;
use App\Post;
use App\Services\MetaBag;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Spatie\Sheets\Sheet;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

Route::get('/', function (MetaBag $meta) {
    $meta->description = 'I\'m an enthusiastic web developer and free time gamer from Hamburg, Germany.';
    $meta->image = mix('images/og/static/home.png');

    return view('pages.home', [
        'me' => sheets('static')->get('me'),
        'streams' => sheets('streams')->all()->sortByDesc('date'),
    ]);
})->name('home');

Route::sheet('/resume', 'pages.resume', 'resume', function (MetaBag $meta, Sheet $data) {
    $meta->title = 'Resume';
    $meta->image = mix('images/og/static/me.png');

    $data->jobs = Job::all();
    $data->hacktoberfests = sheets('hacktoberfest')->all()->sortByDesc('slug');
})->name('resume');

Route::sheet('/uses', 'pages.uses', 'uses', function (MetaBag $meta) {
    $meta->title = 'Uses';
    $meta->description = 'Software and Tools I use in my daily live for development and some little helpers to improve my experience.';
    $meta->image = mix('images/og/static/uses.png');
})->name('uses');

Route::sheet('/charity', 'pages.charity', 'charity', function (MetaBag $meta) {
    $meta->title = 'Charity';
    $meta->description = 'For me it\'s part of my obligation and responsibility to support what I believe is important for me, us and our planet.';
    $meta->image = mix('images/og/static/charity.png');
})->name('charity');

Route::sheet('/portfolio', 'pages.portfolio', 'portfolio', function (MetaBag $meta) {
    $meta->title = 'Portfolio';
    $meta->description = 'In my free time I support several local business owners with everything I know.';
    $meta->image = mix('images/og/static/portfolio.png');
})->name('portfolio');

Route::sheet('/imprint', 'pages.imprint', 'imprint', function (MetaBag $meta) {
    $meta->title = 'Imprint';
})->name('imprint');

Route::sheet('/privacy', 'pages.privacy', 'privacy', function (MetaBag $meta) {
    $meta->title = 'Privacy';
})->name('privacy');

Route::prefix('blog')->name('blog.')->group(function (): void {
    Route::get('search.json', fn (): Jsonable => Post::all()->map(fn (Post $post): array => [
        'url' => $post->url,
        'title' => $post->title,
        'date' => $post->date->format('M jS, Y'),
        'categories' => $post->categories,
        'description' => $post->description,
        'content' => $post->markdown,
    ]))->name('search');
    Route::get('{page?}', Blog\IndexController::class)->middleware(Paginated::class)->name('index');
    Route::get('feed.{format}', Blog\FeedController::class)->name('feed');

    Route::get('{year}/{page?}', Blog\Year\IndexController::class)->middleware(Paginated::class)->name('year.index');

    Route::prefix('@{author}')->name('author.')->group(function (): void {
        Route::get('{page?}', Blog\Author\IndexController::class)->middleware(Paginated::class)->name('index');
        Route::get('feed.{format}', Blog\Author\FeedController::class)->name('feed');
    });

    Route::prefix('{category}')->name('category.')->group(function (): void {
        Route::get('{page?}', Blog\Category\IndexController::class)->middleware(Paginated::class)->name('index');
        Route::get('feed.{format}', Blog\Category\FeedController::class)->name('feed');
    });

    Route::get('{post}', Blog\PostController::class)->name('post');
});

Route::get(
    '404.html',
    function (MetaBag $meta) {
        $meta->title = 'Not Found';

        return view('pages.404');
    }
)->name('404');

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
