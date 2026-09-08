<?php

namespace App\Console\Commands;

use App\Contracts\StreamTranscriptProvider;
use App\Data\YouTubeVideo;
use App\Services\YouTube;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Contracts\Taxonomies\Term as TermContract;
use Statamic\Facades\Entry;
use Statamic\Facades\Term;
use Throwable;

final class SyncStream extends Command
{
    protected $signature = 'stream:sync
        {url : YouTube video URL or video ID}
        {--category= : Category slug to use for a new stream or override on update}';

    protected $description = 'Create or update a stream entry from YouTube metadata.';

    public function handle(YouTube $youtube, StreamTranscriptProvider $transcripts): int
    {
        try {
            $video = $youtube->video((string) $this->argument('url'));
            $entry = $this->findStream($video->id);
            $created = $entry === null;
            $data = $entry?->data()->all() ?? [];

            $data = array_merge($data, [
                'title' => $video->title,
                'video' => $video->url,
                'description' => $video->description,
                'duration' => $video->duration,
                'channel_id' => $video->channelId,
                'channel_name' => $video->channelName,
                'channel_url' => $video->channelUrl,
                'image' => $video->image,
                'youtube_tags' => $video->tags,
            ]);

            $category = $this->option('category');

            if ($entry === null || blank($data['category'] ?? null) || filled($category)) {
                $data['category'] = $this->category(is_string($category) ? $category : null);
            }

            $transcript = $transcripts->fetch($video->id);

            if (filled($transcript)) {
                $data['transcript'] = $this->writeTranscript($video, (string) $transcript);
            }

            $entry ??= Entry::make()
                ->collection('streams')
                ->blueprint('stream')
                ->slug($video->id);

            $entry
                ->date($video->publishedAt)
                ->data($data)
                ->save();

            $this->components->info(sprintf(
                '%s stream %s',
                $created ? 'Created' : 'Updated',
                $entry->url() ?? $video->url,
            ));

            if (! filled($transcript)) {
                $this->components->warn('No transcript provider is configured yet. Metadata was synced without a transcript.');
            }

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }
    }

    private function findStream(string $videoId): ?EntryContract
    {
        return Entry::query()
            ->where('collection', 'streams')
            ->where('slug', $videoId)
            ->first();
    }

    private function category(?string $category): string
    {
        if (filled($category)) {
            $this->validateCategory((string) $category);

            return (string) $category;
        }

        $categories = Term::whereTaxonomy('category')
            ->sortBy(fn (TermContract $term): string => $term->title())
            ->values();

        if ($categories->isEmpty()) {
            throw new RuntimeException('No categories exist.');
        }

        $slug = (string) $this->choice(
            'Category',
            $categories->map(fn (TermContract $term): string => $term->slug())->all(),
        );

        $this->validateCategory($slug);

        return $slug;
    }

    private function validateCategory(string $category): void
    {
        if (Term::find('category::'.$category) === null) {
            throw new RuntimeException("Unknown category [{$category}].");
        }
    }

    private function writeTranscript(YouTubeVideo $video, string $transcript): string
    {
        $relativePath = 'assets/streams/transcripts/'.$video->id.'.txt';
        $path = public_path($relativePath);

        File::ensureDirectoryExists(dirname($path));
        File::put($path, rtrim($transcript).PHP_EOL);

        return $relativePath;
    }
}
