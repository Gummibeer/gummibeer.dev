<?php

namespace App;

use App\Repositories\StreamRepository;
use App\Services\Model;
use Carbon\CarbonInterval;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

final class Stream extends Model implements Feedable
{
    public function getRouteKey()
    {
        return $this->youtube_id;
    }

    public function getDurationAttribute(string $duration): CarbonInterval
    {
        return CarbonInterval::fromString($duration);
    }

    public function getUrlAttribute(mixed $value = null): string
    {
        return "https://youtu.be/{$this->youtube_id}";
    }

    public function getImageAttribute(mixed $value = null): string
    {
        return "https://i.ytimg.com/vi/{$this->youtube_id}/maxresdefault.jpg";
    }

    public function getAuthorAttribute(mixed $value = null): Author
    {
        return Author::find('Gummibeer');
    }

    public function toFeedItem(): FeedItem
    {
        return FeedItem::create()
            ->id($this->url)
            ->title($this->title)
            ->author(sprintf('%s, %s', $this->author->name, $this->author->email))
            ->summary($this->title)
            ->updated($this->date)
            ->link($this->url)
            ->category('stream');
    }

    public static function __callStatic($name, $arguments)
    {
        return app(StreamRepository::class)->{$name}(...$arguments);
    }
}
