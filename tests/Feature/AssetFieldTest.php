<?php

namespace Tests\Feature;

use App\View\Components\Img;
use Illuminate\Support\Collection;
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

        $this->assertInstanceOf(StatamicEntry::class, $post);
        $this->assertSame('posts/2020-12-10.human-readable-intervals.jpg', $post->value('image'));
        $this->assertInstanceOf(Asset::class, $post->image);

        $galleryPost = Entry::query()
            ->where('collection', 'posts')
            ->where('slug', 'yoda')
            ->first();

        $this->assertInstanceOf(StatamicEntry::class, $galleryPost);
        $this->assertSame([
            'posts/2021-01-28.yoda/header-01.jpg',
            'posts/2021-01-28.yoda/header-02.jpg',
            'posts/2021-01-28.yoda/header-03.jpg',
        ], $galleryPost->value('images'));

        $gallery = $galleryPost->images;
        $this->assertInstanceOf(Collection::class, $gallery);
        $gallery->each(fn (mixed $image) => $this->assertInstanceOf(Asset::class, $image));

        $job = Entry::query()
            ->where('collection', 'jobs')
            ->where('slug', 'hospitable')
            ->first();

        $this->assertInstanceOf(StatamicEntry::class, $job);
        $this->assertSame('company/hospitable.png', $job->value('logo'));
        $this->assertInstanceOf(Asset::class, $job->logo);

        $hacktoberfest = Entry::query()
            ->where('collection', 'hacktoberfest')
            ->where('slug', '2023')
            ->first();

        $this->assertInstanceOf(StatamicEntry::class, $hacktoberfest);
        $this->assertSame('hacktoberfest/2023.png', $hacktoberfest->value('image'));
        $this->assertInstanceOf(Asset::class, $hacktoberfest->image);

        $image = new Img($post->image, width: 400);
        $this->assertStringContainsString('/img/asset/', html_entity_decode($image->src()));
    }
}
