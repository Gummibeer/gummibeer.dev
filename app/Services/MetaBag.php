<?php

namespace App\Services;

use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use Statamic\Contracts\Imaging\UrlBuilder;

/**
 * @property string $title
 * @property string $description
 * @property string $image
 */
class MetaBag extends Fluent
{
    private SiteIdentity $identity;

    public function __construct(?SiteIdentity $identity = null)
    {
        parent::__construct([]);

        $this->identity = $identity ?? app(SiteIdentity::class);
        $this->title = $this->identity->siteName();
    }

    public function setTitleAttribute(string $title): string
    {
        $siteName = $this->identity->siteName();

        if (Str::endsWith($title, $siteName)) {
            return $title;
        }

        return implode(' | ', [
            $title,
            $siteName,
        ]);
    }

    public function setImageAttribute(string $image): string
    {
        $path = trim((string) parse_url($image, PHP_URL_PATH), '/');

        if (Str::startsWith($path, 'images/')) {
            $image = app(UrlBuilder::class)->build(
                'images::'.Str::after($path, 'images/'),
                [],
            );
        }

        return url($image);
    }

    public function offsetSet($key, $value): void
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
