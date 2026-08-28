<?php

namespace App;

use App\Repositories\PostRepository;
use App\Services\Model;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

/**
 * @property-read string $title
 * @property-read string|null $image
 * @property-read string[]|null $images
 * @property-read string $image_credits
 * @property-read string[] $categories
 * @property-read Carbon $date
 * @property-read Carbon $modified_at
 * @property-read string $contents
 * @property-read string $markdown
 * @property-read float $read_time
 * @property-read Author $author
 * @property-read string $description
 * @property-read string $slug
 * @property-read string $url
 * @property-read bool $is_draft
 * @property-read bool $should_promote
 * @property Carbon $promoted_at
 *
 * @method static Collection|Post[] all()
 * @method static Post latest()
 * @method static Post find(string $slug)
 * @method static int count()
 * @method static bool isEmpty()
 * @method static bool isNotEmpty()
 */
final class Post extends Model implements Feedable
{
    public function categories(): Collection
    {
        return collect($this->categories)
            ->map(fn (string $slug): array => ['slug' => $slug])
            ->mapInto(Category::class);
    }

    public function getRouteKey()
    {
        return $this->date->year.'/'.$this->slug;
    }

    public function getAuthorAttribute(string $nickname): Author
    {
        return Author::find($nickname);
    }

    public function getReadTimeAttribute(mixed $value = null): float
    {
        $wordCount = mb_strlen(strip_tags($this->contents)) / 5;
        $wordsPerMinute = 60 * 3;
        $minutes = ceil(($wordCount / $wordsPerMinute) * 2) / 2;

        return max(1, $minutes);
    }

    public function getUrlAttribute(mixed $value = null): string
    {
        return route('blog.post', $this);
    }

    public function getImageAttribute(?string $value): ?string
    {
        return $value ?? Arr::first($this->images ?? []);
    }

    public function getModifiedAtAttribute(mixed $value = null): Carbon
    {
        return Carbon::createFromTimestampUTC(filemtime($this->entry()->path()));
    }

    public function getIsDraftAttribute(?bool $value): bool
    {
        return $value ?? false;
    }

    public function getShouldPromoteAttribute(?bool $value): bool
    {
        return $value ?? true;
    }

    public function getPromotedAtAttribute(string|int|CarbonInterface|null $value): ?Carbon
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof CarbonInterface) {
            return Carbon::instance($value);
        }

        return is_numeric($value)
            ? Carbon::createFromTimestampUTC((int) $value)
            : Carbon::parse($value);
    }

    public function toFeedItem(): FeedItem
    {
        return FeedItem::create()
            ->id($this->url)
            ->title($this->title)
            ->author(sprintf('%s, %s', $this->author->name, $this->author->email))
            ->summary($this->description)
            ->updated($this->date)
            ->link($this->url)
            ->category(...$this->categories);
    }

    public function save(): bool
    {
        if ($this->promoted_at !== null) {
            $this->promoted_at = $this->promoted_at->toIso8601String();
        }

        return parent::save();
    }

    public static function __callStatic($name, $arguments)
    {
        return app(PostRepository::class)->{$name}(...$arguments);
    }
}
