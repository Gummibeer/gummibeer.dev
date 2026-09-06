<?php

namespace App\Services;

use Illuminate\Support\Collection;
use RuntimeException;
use Spatie\Feed\Feed as SpatieFeed;
use Spatie\Feed\FeedItem;
use Statamic\Contracts\Auth\User as UserContract;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Entries\Entry as StatamicEntry;
use Statamic\Facades\GlobalSet;
use Statamic\Facades\User;

final class Feed extends SpatieFeed
{
    public static function make(
        string $title,
        string $description,
        Collection $items,
        string $format
    ): self {
        abort_unless(in_array($format, ['rss', 'atom'], true), 404);
        abort_if($items->isEmpty(), 404);

        $identity = GlobalSet::findByHandle('identity');

        if (! $identity) {
            throw new RuntimeException('The Statamic identity global is missing.');
        }

        return new self(
            $title.' | '.$identity->inCurrentSite()->get('site_name'),
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

        $author = $post->author;

        if (! $author instanceof UserContract) {
            throw new RuntimeException('The Statamic post author is missing.');
        }

        $url = (string) $post->absoluteUrl();

        return FeedItem::create()
            ->id($url)
            ->title((string) $post->value('title'))
            ->authorName($author->get('name'))
            ->authorEmail($author->email())
            ->summary((string) $post->value('description'))
            ->updated($post->date())
            ->link($url)
            ->category((string) $post->value('category'));
    }

    public static function streamItem(EntryContract $stream): FeedItem
    {
        $author = self::defaultAuthor();
        $url = (string) $stream->value('video');

        return FeedItem::create()
            ->id($url)
            ->title((string) $stream->value('title'))
            ->authorName($author->get('name'))
            ->authorEmail($author->email())
            ->summary((string) $stream->value('title'))
            ->updated($stream->date())
            ->link($url)
            ->category('stream');
    }

    private static function defaultAuthor(): UserContract
    {
        $author = User::find('gummibeer');

        if (! $author instanceof UserContract) {
            throw new RuntimeException('The default Statamic author user is missing.');
        }

        return $author;
    }
}
