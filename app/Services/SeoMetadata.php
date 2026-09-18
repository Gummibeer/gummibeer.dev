<?php

namespace App\Services;

use DateTime;
use DateTimeInterface;
use Honeystone\Seo\OpenGraph\ArticleProperties;
use Honeystone\Seo\OpenGraph\ImageProperties;
use Honeystone\Seo\OpenGraph\ProfileProperties;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\View\Cascade;

class SeoMetadata
{
    public function configure(Cascade $cascade): void
    {
        $page = $cascade->content();
        $entry = $page instanceof EntryContract ? $page : null;
        $name = trim((string) data_get($cascade->get('identity'), 'name', config('app.name')));

        if ($name === '') {
            $name = (string) config('app.name');
        }

        $title = trim((string) $entry?->value('title'));
        $title = $title !== '' ? $title : $name;
        $socialTitle = $title === $name ? $name : $title.' | '.$name;
        $description = trim((string) $entry?->value('description'));
        $url = $entry?->absoluteUrl() ?? request()->url();
        $imagePath = trim((string) $entry?->value('og_image'));
        $image = $imagePath !== '' ? OpenGraphImage::url($imagePath) : null;

        seo()
            ->title(
                $title,
                template: $title === $name ? false : '{title} | '.$name,
            )
            ->description($description !== '' ? $description : null)
            ->url($url)
            ->locale(str_replace('-', '_', app()->getLocale()))
            ->openGraphSite($name)
            ->openGraphType('website')
            ->openGraphTitle($socialTitle)
            ->twitterCard($image ? 'summary_large_image' : 'summary')
            ->twitterTitle($socialTitle)
            ->jsonLdType('WebPage');

        if ($image) {
            seo()
                ->images($image)
                ->twitterImage($image, $title)
                ->openGraphImage(new ImageProperties(
                    url: $image,
                    alt: $title,
                    width: (string) OpenGraphImage::WIDTH,
                    height: (string) OpenGraphImage::HEIGHT,
                    type: 'image/png',
                ));
        }

        if ($entry?->collection()->handle() === 'posts') {
            $publishedAt = $this->toDateTime($entry->date());
            $modifiedAt = $this->toDateTime($entry->value('last_modified_at'));

            seo()
                ->openGraphType(new ArticleProperties(
                    publishedTime: $publishedAt,
                    modifiedTime: $modifiedAt,
                ))
                ->jsonLdType('BlogPosting')
                ->jsonLdProperty('author', [
                    '@type' => 'Person',
                    'name' => $name,
                    'url' => url('/'),
                ]);

            if ($publishedAt) {
                seo()->jsonLdProperty('datePublished', $publishedAt->format(DATE_ATOM));
            }

            if ($modifiedAt) {
                seo()->jsonLdProperty('dateModified', $modifiedAt->format(DATE_ATOM));
            }
        }

        if ($entry?->template() === 'pages/resume') {
            seo()
                ->openGraphType(new ProfileProperties)
                ->jsonLdType('ProfilePage')
                ->jsonLdProperty('mainEntity', [
                    '@type' => 'Person',
                    'name' => $name,
                    'url' => url('/'),
                ]);
        }
    }

    private function toDateTime(mixed $value): ?DateTime
    {
        if ($value instanceof DateTime) {
            return $value;
        }

        return $value instanceof DateTimeInterface
            ? DateTime::createFromInterface($value)
            : null;
    }
}
