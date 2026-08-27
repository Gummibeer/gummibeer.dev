<?php

namespace App\Services;

use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use League\CommonMark\ConverterInterface;
use Statamic\Contracts\Entries\Entry;

abstract class Model implements Arrayable, UrlRoutable
{
    protected array $attributes = [];

    protected ?Entry $entry = null;

    public function __construct(array|Entry $attributes = [])
    {
        if ($attributes instanceof Entry) {
            $this->hydrateFromEntry($attributes);

            return;
        }

        $this->attributes = $attributes;
    }

    public function __get(string $name): mixed
    {
        $value = $this->attributes[$name] ?? null;
        $accessor = 'get'.Str::studly($name).'Attribute';

        return method_exists($this, $accessor)
            ? $this->{$accessor}($value)
            : $value;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return $this->__get($name) !== null;
    }

    public function toArray(): array
    {
        return collect($this->attributes)
            ->mapWithKeys(fn (mixed $value, string $key): array => [$key => $this->{$key}])
            ->all();
    }

    public function getRouteKey()
    {
        return $this->slug;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if ($field !== null) {
            return static::all()->firstWhere($field, $value);
        }

        return static::find($value);
    }

    public function resolveChildRouteBinding($childType, $value, $field)
    {
        return $this->resolveRouteBinding($value, $field);
    }

    public function entry(): ?Entry
    {
        return $this->entry;
    }

    public function save(): bool
    {
        if ($this->entry === null) {
            return false;
        }

        $data = Arr::except($this->attributes, [
            'contents',
            'date',
            'markdown',
            'path',
            'slug',
        ]);

        $data['content'] = $this->attributes['markdown'] ?? '';

        $this->entry->data($data);

        return $this->entry->save();
    }

    protected function hydrateFromEntry(Entry $entry): void
    {
        $this->entry = $entry;

        $markdown = (string) ($entry->value('content') ?? '');

        $attributes = $entry->data()
            ->except('content')
            ->merge([
                'slug' => $entry->slug(),
                'path' => $entry->path(),
                'markdown' => $markdown,
                'contents' => new HtmlString((string) app(ConverterInterface::class)->convert($markdown)),
            ]);

        if ($entry->date() !== null) {
            $attributes->put('date', $entry->date());
        }

        $this->attributes = $attributes->all();
    }
}
