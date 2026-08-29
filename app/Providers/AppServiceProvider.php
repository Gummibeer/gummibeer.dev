<?php

namespace App\Providers;

use App\Services\FencedCodeRenderer;
use App\Services\ImageRenderer;
use App\Services\OgImage;
use App\Services\ParagraphRenderer;
use App\Services\SiteIdentity;
use Astrotomic\Pixpipe\Manipulators\Size as PixpipeSize;
use Carbon\CarbonInterval;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use League\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Image;
use League\Glide\Api\Api;
use League\Glide\Manipulators\ManipulatorInterface;
use League\Glide\Manipulators\Size;
use League\Glide\Server;
use LogicException;
use Statamic\Contracts\Assets\Asset as AssetContract;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Entries\Entry as StatamicEntry;
use Statamic\Facades\Collection as StatamicCollection;
use Statamic\Facades\Markdown;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SiteIdentity::class);
        $this->app->singleton(OgImage::class);

        $this->registerPixpipeGlide();
    }

    public function boot(): void
    {
        View::share('identity', $this->app->make(SiteIdentity::class));

        Paginator::useTailwind();

        $this->registerComputedContentValues();
        $this->registerMarkdown();
    }

    public function registerComputedContentValues(): void
    {
        $ogImage = $this->app->make(OgImage::class);

        StatamicCollection::computed('pages', [
            'seo_image' => static fn (EntryContract $entry, mixed $value): ?AssetContract => $ogImage->forPage($entry),
        ]);

        StatamicCollection::computed('posts', [
            'image' => static function (EntryContract $entry, mixed $value): ?string {
                $images = $entry->value('images');

                return $value ?? (is_array($images) ? Arr::first($images) : null);
            },
            'seo_image' => static fn (EntryContract $entry, mixed $value): ?AssetContract => $ogImage->forPost($entry),
            'public_url' => static function (EntryContract $entry, mixed $value): string {
                if (! $entry instanceof StatamicEntry) {
                    throw new LogicException('Expected a concrete Statamic post entry.');
                }

                return (string) $entry->absoluteUrl();
            },
            'read_time' => static function (EntryContract $entry, mixed $value): float {
                $html = Markdown::parse((string) $entry->value('content'));
                $wordCount = mb_strlen(strip_tags($html)) / 5;
                $wordsPerMinute = 60 * 3;
                $minutes = ceil(($wordCount / $wordsPerMinute) * 2) / 2;

                return max(1, $minutes);
            },
        ]);

        StatamicCollection::computed('streams', [
            'duration' => static fn (EntryContract $entry, mixed $value): CarbonInterval => CarbonInterval::fromString((string) $value),
            'external_url' => static fn (EntryContract $entry, mixed $value): string => 'https://youtu.be/'.$entry->value('youtube_id'),
            'image' => static fn (EntryContract $entry, mixed $value): string => 'https://i.ytimg.com/vi/'.$entry->value('youtube_id').'/maxresdefault.jpg',
        ]);

        StatamicCollection::computed('jobs', [
            'website_host' => static fn (EntryContract $entry, mixed $value): string => (string) parse_url((string) $entry->value('website'), PHP_URL_HOST),
            'icon_class' => static fn (EntryContract $entry, mixed $value): string => 'fa-solid '.Str::start((string) $entry->value('icon'), 'fa-'),
            'has_end' => static fn (EntryContract $entry, mixed $value): bool => filled($entry->value('end_at')),
        ]);
    }

    public function registerMarkdown(): void
    {
        Markdown::addRenderers(fn (): array => [
            [FencedCode::class, new FencedCodeRenderer, 10],
            [Paragraph::class, new ParagraphRenderer, 10],
            [Image::class, new ImageRenderer, 10],
        ]);
    }

    public function registerPixpipeGlide(): void
    {
        $this->app->extend(Server::class, function (Server $server): Server {
            $api = $server->getApi();

            if (! $api instanceof Api) {
                throw new LogicException('Statamic Glide must use the concrete League Glide API.');
            }

            $manipulators = array_map(
                fn (ManipulatorInterface $manipulator): ManipulatorInterface => $manipulator instanceof Size
                    ? new PixpipeSize
                    : $manipulator,
                $api->getManipulators(),
            );

            $api->setManipulators($manipulators);
            $server->setApi($api);

            return $server;
        });
    }
}
