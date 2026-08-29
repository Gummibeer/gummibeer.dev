<?php

namespace App;

use App\Repositories\StreamRepository;
use App\Services\Model;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

/**
 * @property-read string $title
 * @property-read string $image
 * @property-read Carbon $date
 * @property-read string $youtube_id
 * @property-read CarbonInterval $duration
 * @property-read string $url
 * @property-read Author $author
 */
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

    public function getUrlAttribute(): string
    {
        return "https://youtu.be/{$this->youtube_id}";
    }

    public function getImageAttribute(): string
    {
        return "https://i.ytimg.com/vi/{$this->youtube_id}/maxresdefault.jpg";
    }

    public function getAuthorAttribute(): Author
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
        return call_user_func_array([app(StreamRepository::class), $name], $arguments);
    }
}
