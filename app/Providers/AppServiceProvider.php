<?php

namespace App\Providers;

use App\Contracts\StreamTranscriptProvider;
use App\Http\Middleware\AutoLoginStatamicControlPanel;
use App\Markdown\MarkdownExtension;
use App\Services\OpenGraphImage;
use App\Services\ReadingTime;
use App\Services\YouTubeTranscript;
use Astrotomic\Pixpipe\Manipulators\Size as PixpipeSize;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use DateInterval;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use League\Glide\Api\Api;
use League\Glide\Manipulators\ManipulatorInterface;
use League\Glide\Manipulators\Size;
use League\Glide\Server;
use LogicException;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Facades\Collection as StatamicCollection;
use Statamic\Facades\Markdown;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ReadingTime::class);
        $this->app->bind(StreamTranscriptProvider::class, YouTubeTranscript::class);
        $this->registerPixpipeGlide();
    }

    public function boot(Router $router): void
    {
        $this->app->booted(
            fn () => $router->pushMiddlewareToGroup('statamic.cp', AutoLoginStatamicControlPanel::class),
        );

        Paginator::useTailwind();

        $this->registerComputedContentValues();
        $this->registerMarkdown();
    }

    public function registerComputedContentValues(): void
    {
        $ogImage = static fn (EntryContract $entry, mixed $value): string => (string) ($value ?? OpenGraphImage::path($entry));

        StatamicCollection::computed('pages', [
            'og_image' => $ogImage,
        ]);

        StatamicCollection::computed('posts', [
            'og_image' => $ogImage,
            'image' => static function (EntryContract $entry, mixed $value): ?string {
                $images = $entry->value('images');

                return $value ?? (is_array($images) ? Arr::first($images) : null);
            },
            'read_time' => static function (EntryContract $entry, mixed $value): CarbonInterval {
                return app(ReadingTime::class)->estimate((string) $entry->value('content'));
            },
            'last_modified_at' => static function (EntryContract $entry, mixed $value): ?CarbonImmutable {
                $modifiedAt = $entry->value('updated_at') ?? $entry->date() ?? filemtime($entry->path());

                return CarbonImmutable::make($modifiedAt);
            },
        ]);

        StatamicCollection::computed('streams', [
            'og_image' => $ogImage,
            'duration' => static fn (EntryContract $entry, mixed $value): CarbonInterval => self::streamDuration((string) $value),
            'read_time' => static fn (EntryContract $entry, mixed $value): CarbonInterval => self::streamDuration((string) $entry->value('duration')),
            'transcript_text' => static fn (EntryContract $entry, mixed $value): ?string => self::streamTranscript($entry),
        ]);

        StatamicCollection::computed('jobs', [
            'website_host' => static fn (EntryContract $entry, mixed $value): string => (string) parse_url((string) $entry->value('website'), PHP_URL_HOST),
            'icon_class' => static fn (EntryContract $entry, mixed $value): string => Str::start((string) $entry->value('icon'), 'ski-'),
            'has_end' => static fn (EntryContract $entry, mixed $value): bool => filled($entry->value('end_at')),
        ]);
    }

    public function registerMarkdown(): void
    {
        Markdown::addExtension(fn (): MarkdownExtension => new MarkdownExtension);
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

    private static function streamDuration(string $value): CarbonInterval
    {
        return str_starts_with($value, 'P')
            ? new CarbonInterval(new DateInterval($value))
            : CarbonInterval::fromString($value);
    }

    private static function streamTranscript(EntryContract $entry): ?string
    {
        $path = (string) $entry->value('transcript');
        if (blank($path)) {
            return null;
        }

        $root = realpath(public_path('assets/streams/transcripts'));
        $resolved = realpath(public_path($path));

        if (
            $root === false
            || $resolved === false
            || ! str_starts_with($resolved, $root.DIRECTORY_SEPARATOR)
            || ! is_file($resolved)
        ) {
            return null;
        }

        return File::get($resolved);
    }
}
