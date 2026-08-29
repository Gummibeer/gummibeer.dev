<?php

namespace App\Tags;

use Astrotomic\OpenGraph\OpenGraph as OpenGraphBuilder;
use Astrotomic\OpenGraph\StructuredProperties\Image;
use Astrotomic\OpenGraph\Twitter;
use Astrotomic\OpenGraph\Types\Article;
use Astrotomic\OpenGraph\Types\Twitter\SummaryLargeImage;
use DateTime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Statamic\Contracts\Assets\Asset;
use Statamic\Facades\URL;
use Statamic\Tags\Tags;
use Stringable;
use Traversable;

class OpenGraph extends Tags
{
    /**
     * Render Open Graph and Twitter Card metadata from the current entry.
     */
    public function index(): string
    {
        $title = $this->text($this->context->value('seo_title'))
            ?: $this->text($this->context->value('title'))
            ?: $this->text($this->params->get('site_name'));
        $description = $this->text($this->context->value('meta_description'))
            ?: $this->text($this->params->get('default_description'));
        $siteName = $this->text($this->params->get('site_name'));
        $url = $this->url($this->context->value('permalink') ?: URL::getCurrent());
        $locale = str_replace('-', '_', $this->text($this->params->get('locale')) ?: 'en_US');
        $isArticle = $this->isArticle();
        $asset = $this->firstAsset(
            $this->context->value('open_graph_image'),
            $isArticle ? $this->context->value('card_image') : $this->context->value('hero_image'),
            $this->params->get('default_image'),
        );

        $openGraph = $isArticle
            ? OpenGraphBuilder::article($title)
            : OpenGraphBuilder::website($title);

        $openGraph
            ->url($url)
            ->locale($locale)
            ->when($description)->description($description)
            ->when($siteName)->siteName($siteName);

        if ($asset) {
            $openGraph->image($this->openGraphImage($asset, $title));
        }

        if ($openGraph instanceof Article) {
            $this->addArticleMetadata($openGraph);
        }

        $twitter = $asset
            ? Twitter::summaryLargeImage($title)->image($this->url($asset->absoluteUrl()), $this->imageAlt($asset, $title))
            : Twitter::summary($title);

        $twitter
            ->when($description)->description($description)
            ->when($handle = $this->twitterHandle())->site($handle);

        if ($twitter instanceof SummaryLargeImage) {
            $twitter->when($handle)->creator($handle);
        }

        return $openGraph.PHP_EOL.$twitter;
    }

    private function openGraphImage(Asset $asset, string $fallbackAlt): Image
    {
        $image = Image::make($this->url($asset->absoluteUrl()), false);

        if (str_starts_with((string) $asset->absoluteUrl(), 'https://')) {
            $image->secureUrl($this->url($asset->absoluteUrl()));
        }

        if ($mimeType = $this->text($asset->mimeType())) {
            $image->mimeType($mimeType);
        }

        if ($width = $asset->width()) {
            $image->width((int) $width);
        }

        if ($height = $asset->height()) {
            $image->height((int) $height);
        }

        return $image->alt($this->imageAlt($asset, $fallbackAlt));
    }

    private function addArticleMetadata(Article $openGraph): void
    {
        if ($publishedAt = $this->date($this->context->value('date'))) {
            $openGraph->publishedAt($publishedAt);
        }

        if ($modifiedAt = $this->date($this->context->value('updated_at'))) {
            $openGraph->modifiedAt($modifiedAt);
        }

        $categories = $this->context->value('categories');

        if (is_string($categories)) {
            $categories = [$categories];
        } elseif ($categories instanceof Traversable) {
            $categories = iterator_to_array($categories);
        }

        foreach (is_array($categories) ? $categories : [] as $index => $category) {
            if (! $category = $this->text($category)) {
                continue;
            }

            $openGraph->tag($category);

            if ($index === 0) {
                $openGraph->section($category);
            }
        }

        if ($authorUrl = $this->url($this->params->get('author_url'))) {
            $openGraph->author($authorUrl);
        }
    }

    private function firstAsset(mixed ...$values): ?Asset
    {
        foreach ($values as $value) {
            if ($value instanceof Asset) {
                return $value;
            }

            if ($value instanceof Collection) {
                $value = $value->first();
            } elseif ($value instanceof Traversable) {
                foreach ($value as $item) {
                    $value = $item;
                    break;
                }
            } elseif (is_array($value)) {
                $value = reset($value);
            }

            if ($value instanceof Asset) {
                return $value;
            }
        }

        return null;
    }

    private function isArticle(): bool
    {
        if ($this->text($this->context->value('template')) === 'article') {
            return true;
        }

        $collection = $this->context->value('collection');
        $handle = is_object($collection) && method_exists($collection, 'handle')
            ? $collection->handle()
            : $collection;

        return $this->text($handle) === 'articles';
    }

    private function imageAlt(Asset $asset, string $fallback): string
    {
        return $this->text($asset->get('alt')) ?: $fallback;
    }

    private function twitterHandle(): string
    {
        $handle = ltrim($this->text($this->params->get('twitter_handle')), '@');

        return $handle === '' ? '' : '@'.$handle;
    }

    private function date(mixed $value): ?DateTime
    {
        if ($value instanceof DateTime) {
            return $value;
        }

        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function url(mixed $value): string
    {
        $value = $this->text($value);

        return $value === '' ? '' : $this->text(URL::makeAbsolute($value));
    }

    private function text(mixed $value): string
    {
        if (! is_scalar($value) && ! $value instanceof Stringable) {
            return '';
        }

        $value = html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = preg_replace('/\s+/u', ' ', strip_tags($value)) ?? '';

        return htmlspecialchars(trim($value), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    }
}
