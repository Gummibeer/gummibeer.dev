<?php

namespace App\Services;

use Illuminate\Support\Collection;
use RuntimeException;
use Spatie\Feed\Feed as SpatieFeed;
use Spatie\Feed\FeedItem;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Entries\Entry as StatamicEntry;
use Statamic\Facades\Entry;

class Feed extends SpatieFeed
{
    public static function make(
        string $title,
        string $description,
        Collection $items,
        string $format
    ): self {
        abort_unless(in_array($format, ['rss', 'atom']), 404);
        abort_if($items->isEmpty(), 404);

        return new static(
            $title.' | '.app(SiteIdentity::class)->siteName(),
            $items,
            request()->url(),
            'feed::'.$format,
            $description,
            app()->getLocale(),
            '',
            $format
        );
    }

    public static function postItem(EntryContract $post): FeedItem
    {
        if (! $post instanceof StatamicEntry) {
            throw new RuntimeException('Expected a concrete Statamic post entry.');
        }

        $author = self::author($post);
        $categories = $post->value('categories');
        $url = (string) $post->absoluteUrl();

        return FeedItem::create()
            ->id($url)
            ->title((string) $post->value('title'))
            ->author(sprintf('%s, %s', $author->value('title'), $author->value('email')))
            ->summary((string) $post->value('description'))
            ->updated($post->date())
            ->link($url)
            ->category(...(is_array($categories) ? $categories : []));
    }

    public static function streamItem(EntryContract $stream): FeedItem
    {
        $author = self::author($stream);
        $url = 'https://youtu.be/'.$stream->value('youtube_id');

        return FeedItem::create()
            ->id($url)
            ->title((string) $stream->value('title'))
            ->author(sprintf('%s, %s', $author->value('title'), $author->value('email')))
            ->summary((string) $stream->value('title'))
            ->updated($stream->date())
            ->link($url)
            ->category('stream');
    }

    private static function author(EntryContract $entry): EntryContract
    {
        $authorId = $entry->value('author');
        $author = is_string($authorId) ? Entry::find($authorId) : null;
        $author ??= Entry::find('gummibeer');

        if (! $author instanceof EntryContract) {
            throw new RuntimeException('The default Statamic author entry is missing.');
        }

        return $author;
    }
}
