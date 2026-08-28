<?php

use App\Http\Controllers\Blog;
use App\Http\Middleware\Paginated;
use App\Services\MetaBag;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Exceptions\NotFoundHttpException;
use Statamic\Facades\Entry;

$findPage = static function (string $slug): EntryContract {
    return Entry::query()
        ->where('collection', 'pages')
        ->where('slug', $slug)
        ->firstOr(fn () => throw new NotFoundHttpException);
};

Route::get('/', function (MetaBag $meta) use ($findPage) {
    $meta->description = 'I\'m an enthusiastic web developer and free time gamer from Hamburg, Germany.';
    $meta->image = mix('images/og/static/home.png');

    return view('pages.home', [
        'me' => $findPage('me'),
        'posts' => Entry::query()
            ->where('collection', 'posts')
            ->whereStatus('published')
            ->get()
            ->sortByDesc(fn ($entry) => $entry->date())
            ->values(),
        'streams' => Entry::query()
            ->where('collection', 'streams')
            ->whereStatus('published')
            ->get()
            ->sortByDesc(fn ($entry) => $entry->date())
            ->values(),
    ]);
})->name('home');

Route::get('/resume', function (MetaBag $meta) use ($findPage) {
    $meta->title = 'Resume';
    $meta->image = mix('images/og/static/me.png');

    $page = $findPage('resume');
    $jobs = Entry::whereCollection('jobs')
        ->sort(function (EntryContract $a, EntryContract $b): int {
            $aHasEnd = filled($a->value('end_at'));
            $bHasEnd = filled($b->value('end_at'));

            if ($aHasEnd === $bHasEnd) {
                return Carbon::parse($b->value('start_at'))->timestamp <=> Carbon::parse($a->value('start_at'))->timestamp;
            }

            return $aHasEnd ? 1 : -1;
        })
        ->values();

    return view('pages.resume', [
        'contents' => $page->content,
        'jobs' => $jobs,
        'hacktoberfests' => Entry::whereCollection('hacktoberfest')->sortByDesc(fn (EntryContract $entry) => $entry->slug()),
    ]);
})->name('resume');

Route::get('/uses', function (MetaBag $meta) use ($findPage) {
    $meta->title = 'Uses';
    $meta->description = 'Software and Tools I use in my daily live for development and some little helpers to improve my experience.';
    $meta->image = mix('images/og/static/uses.png');

    return view('pages.uses', ['contents' => $findPage('uses')->content]);
})->name('uses');

Route::get('/charity', function (MetaBag $meta) use ($findPage) {
    $meta->title = 'Charity';
    $meta->description = 'For me it\'s part of my obligation and responsibility to support what I believe is important for me, us and our planet.';
    $meta->image = mix('images/og/static/charity.png');

    return view('pages.charity', ['contents' => $findPage('charity')->content]);
})->name('charity');

Route::get('/portfolio', function (MetaBag $meta) use ($findPage) {
    $meta->title = 'Portfolio';
    $meta->description = 'In my free time I support several local business owners with everything I know.';
    $meta->image = mix('images/og/static/portfolio.png');

    return view('pages.portfolio', ['contents' => $findPage('portfolio')->content]);
})->name('portfolio');

Route::get('/imprint', function (MetaBag $meta) use ($findPage) {
    $meta->title = 'Imprint';

    return view('pages.imprint', ['contents' => $findPage('imprint')->content]);
})->name('imprint');

Route::get('/privacy', function (MetaBag $meta) use ($findPage) {
    $meta->title = 'Privacy';

    return view('pages.privacy', ['contents' => $findPage('privacy')->content]);
})->name('privacy');

Route::prefix('blog')->name('blog.')->group(function (): void {
    Route::get('search.json', function (): Jsonable {
        return Entry::query()
            ->where('collection', 'posts')
            ->whereStatus('published')
            ->get()
            ->sortByDesc(fn ($post) => $post->date())
            ->values()
            ->map(fn ($post): array => [
                'url' => route('blog.post', ['year' => $post->date()?->year, 'post' => $post->slug()]),
                'title' => $post->value('title'),
                'date' => $post->date()?->format('M jS, Y'),
                'categories' => $post->value('categories') ?? [],
                'description' => $post->value('description'),
                'content' => $post->value('content'),
            ]);
    })->name('search');

    Route::get('feed.{format}', Blog\FeedController::class)->name('feed');
    Route::get('{page?}', Blog\IndexController::class)->middleware(Paginated::class)->name('index');

    Route::get('{year}/{post}', Blog\PostController::class)->name('post');
    Route::get('{year}/{page?}', Blog\Year\IndexController::class)->middleware(Paginated::class)->name('year.index');

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
