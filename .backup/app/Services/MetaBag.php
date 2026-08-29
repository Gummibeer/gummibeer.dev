<?php

namespace App\Services;

use Illuminate\Support\Fluent;
use Illuminate\Support\Str;

/**
 * @property string $title
 * @property string $description
 * @property string $image
 */
class MetaBag extends Fluent
{
    public function __construct()
    {
        parent::__construct([]);
        $this->title = config('app.name');
    }

    public function setTitleAttribute(string $title): string
    {
        if (Str::endsWith($title, config('app.name'))) {
            return $title;
        }

        return implode(' | ', [
            $title,
            config('app.name'),
        ]);
    }

    public function setImageAttribute(string $image): string
    {
        return url(asset($image));
    }

    public function offsetSet($key, $value)
    {
        if ($this->hasSetMutator($key)) {
            $this->attributes[$key] = $this->{'set'.Str::studly($key).'Attribute'}($value);

            return;
        }

        $this->attributes[$key] = $value;
    }

    protected function hasSetMutator(string $key): bool
    {
        return method_exists($this, 'set'.Str::studly($key).'Attribute');
    }
}
