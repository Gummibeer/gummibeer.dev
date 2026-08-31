<?php

namespace Tests\Feature;

use App\View\Components\Img;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Assert;
use Statamic\Assets\Asset;
use Statamic\Entries\Entry as StatamicEntry;
use Statamic\Facades\Entry;
use Tests\TestCase;

final class AssetFieldTest extends TestCase
{
    public function test_managed_content_images_are_native_statamic_assets(): void
    {
        $post = Entry::query()
            ->where('collection', 'posts')
            ->where('slug', 'human-readable-intervals')
            ->first();

        Assert::assertInstanceOf(StatamicEntry::class, $post);
        Assert::assertSame('posts/2020-12-10.human-readable-intervals.jpg', $post->value('image'));
        Assert::assertInstanceOf(Asset::class, $post->image);

        $galleryPost = Entry::query()
            ->where('collection', 'posts')
            ->where('slug', 'yoda')
            ->first();

        Assert::assertInstanceOf(StatamicEntry::class, $galleryPost);
        Assert::assertSame([
            'posts/2021-01-28.yoda/header-01.jpg',
            'posts/2021-01-28.yoda/header-02.jpg',
            'posts/2021-01-28.yoda/header-03.jpg',
        ], $galleryPost->value('images'));

        $gallery = $galleryPost->images;
        Assert::assertInstanceOf(Collection::class, $gallery);
        $gallery->each(fn (mixed $image) => Assert::assertInstanceOf(Asset::class, $image));

        $job = Entry::query()
            ->where('collection', 'jobs')
            ->where('slug', 'hospitable')
            ->first();

        Assert::assertInstanceOf(StatamicEntry::class, $job);
        Assert::assertSame('company/hospitable.png', $job->value('logo'));
        Assert::assertInstanceOf(Asset::class, $job->logo);

        $hacktoberfest = Entry::query()
            ->where('collection', 'hacktoberfest')
            ->where('slug', '2023')
            ->first();

        Assert::assertInstanceOf(StatamicEntry::class, $hacktoberfest);
        Assert::assertSame('hacktoberfest/2023.png', $hacktoberfest->value('image'));
        Assert::assertInstanceOf(Asset::class, $hacktoberfest->image);

        $image = new Img($post->image, width: 400);
        Assert::assertStringContainsString('/img/asset/', html_entity_decode($image->src()));
    }
}
