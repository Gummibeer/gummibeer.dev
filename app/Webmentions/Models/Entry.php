<?php

namespace App\Webmentions\Models;

use Carbon\Carbon;
use Illuminate\Support\HtmlString;

abstract class Entry extends Model
{
    public int $id;

    public string $url;

    public string $source;

    public string $target;

    public ?Carbon $published_at;

    public Carbon $created_at;

    public Author $author;

    public array $raw;

    public ?string $text = null;

    public ?HtmlString $html = null;

    public static function make(array $entry): ?self
    {
        return match ($entry['wm-property']) {
            'like-of' => Like::fromWebmention($entry),
            'repost-of' => Repost::fromWebmention($entry),
            'mention-of' => Mention::fromWebmention($entry),
            'in-reply-to' => Reply::fromWebmention($entry),
            default => null,
        };
    }

    public static function fromWebmention(array $entry): self
    {
        return new static([
            'id' => $entry['wm-id'],
            'url' => $entry['url'],
            'source' => $entry['wm-source'],
            'target' => $entry['wm-target'],
            'published_at' => $entry['published']
                ? Carbon::parse($entry['published'])
                : null,
            'created_at' => $entry['published']
                ? Carbon::parse($entry['published'])
                : Carbon::parse($entry['wm-received']),
            'author' => Author::fromWebmention($entry['author']),
            'text' => $entry['content']['text'] ?? null,
            'html' => isset($entry['content']['html'])
                ? new HtmlString($entry['content']['html'])
                : null,
            'raw' => $entry,
        ]);
    }
}
