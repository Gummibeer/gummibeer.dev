<?php

namespace App\Services;

use LogicException;
use Statamic\Contracts\Assets\Asset as AssetContract;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Facades\Asset;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;
use Statamic\Support\Str;

final class OgImage
{
    public function pagePath(EntryContract $page): string
    {
        $slug = $page->url() === '/' ? 'home' : $page->slug();

        return "images/og/static/{$slug}.png";
    }

    public function postPath(EntryContract $post): string
    {
        $date = $post->date();

        if ($date === null) {
            throw new LogicException('OG images for posts require a dated entry.');
        }

        return sprintf('images/og/posts/%s.%s.png', $date->format('Y-m-d'), $post->slug());
    }

    public function forPage(EntryContract $page): ?AssetContract
    {
        return $this->asset($this->pagePath($page));
    }

    public function forPost(EntryContract $post): ?AssetContract
    {
        return $this->asset($this->postPath($post));
    }

    public function forCollectionMount(string $collection): ?AssetContract
    {
        $mount = Collection::findByHandle($collection)?->mount();

        if (! is_string($mount) || ! $entry = Entry::find($mount)) {
            return null;
        }

        return $this->forPage($entry);
    }

    private function asset(string $path): ?AssetContract
    {
        return Asset::find('images::'.Str::after($path, 'images/'));
    }
}
