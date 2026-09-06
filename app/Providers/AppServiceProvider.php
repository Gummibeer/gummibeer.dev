<?php

namespace App\Providers;

use App\Http\Middleware\AutoLoginStatamicControlPanel;
use App\Listeners\StoreComment;
use App\Services\FencedCodeRenderer;
use App\Services\ImageRenderer;
use App\Services\ParagraphRenderer;
use Astrotomic\Pixpipe\Manipulators\Size as PixpipeSize;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Node\Block\Paragraph;
use League\Glide\Api\Api;
use League\Glide\Manipulators\ManipulatorInterface;
use League\Glide\Manipulators\Size;
use League\Glide\Server;
use LogicException;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Events\FormSubmitted;
use Statamic\Facades\Collection as StatamicCollection;
use Statamic\Facades\Markdown;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerPixpipeGlide();
    }

    public function boot(Router $router): void
    {
        $this->app->booted(
            fn () => $router->pushMiddlewareToGroup('statamic.cp', AutoLoginStatamicControlPanel::class),
        );

        Event::listen(FormSubmitted::class, StoreComment::class);

        Paginator::useTailwind();

        $this->registerComputedContentValues();
        $this->registerMarkdown();
    }

    public function registerComputedContentValues(): void
    {
        StatamicCollection::computed('posts', [
            'image' => static function (EntryContract $entry, mixed $value): ?string {
                $images = $entry->value('images');

                return $value ?? (is_array($images) ? Arr::first($images) : null);
            },
            'read_time' => static function (EntryContract $entry, mixed $value): float {
                $html = Markdown::parse((string) $entry->value('content'));
                $wordCount = mb_strlen(strip_tags($html)) / 5;
                $wordsPerMinute = 60 * 3;
                $minutes = ceil(($wordCount / $wordsPerMinute) * 2) / 2;

                return max(1, $minutes);
            },
            'last_modified_at' => static function (EntryContract $entry, mixed $value): ?CarbonImmutable {
                $modifiedAt = $entry->value('updated_at') ?? $entry->date() ?? filemtime($entry->path());

                return CarbonImmutable::make($modifiedAt);
            },
        ]);

        StatamicCollection::computed('streams', [
            'duration' => static fn (EntryContract $entry, mixed $value): CarbonInterval => CarbonInterval::fromString((string) $value),
            'image' => static function (EntryContract $entry, mixed $value): string {
                $videoId = basename((string) parse_url((string) $entry->value('video'), PHP_URL_PATH));

                return 'https://i.ytimg.com/vi/'.$videoId.'/maxresdefault.jpg';
            },
        ]);

        StatamicCollection::computed('jobs', [
            'website_host' => static fn (EntryContract $entry, mixed $value): string => (string) parse_url((string) $entry->value('website'), PHP_URL_HOST),
            'icon_class' => static fn (EntryContract $entry, mixed $value): string => Str::start((string) $entry->value('icon'), 'ski-'),
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
