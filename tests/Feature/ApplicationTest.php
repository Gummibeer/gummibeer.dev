<?php

namespace Tests\Feature;

use App\Services\MetaBag;
use App\View\Components\Img;
use Astrotomic\Pixpipe\Manipulators\Size as PixpipeSize;
use Carbon\CarbonInterface;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;
use League\Glide\Manipulators\Size;
use League\Glide\Server;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Facades\Asset;
use Statamic\Facades\AssetContainer;
use Statamic\Facades\Entry;
use Statamic\Facades\Markdown;
use Tests\TestCase;

final class ApplicationTest extends TestCase
{
    public function test_native_statamic_content_model_is_populated(): void
    {
        $expected = [
            'posts' => 19,
            'drafts' => 10,
            'streams' => 13,
            'jobs' => 9,
            'hacktoberfest' => 6,
            'pages' => 7,
            'authors' => 1,
        ];

        foreach ($expected as $collection => $count) {
            $this->assertSame($count, Entry::whereCollection($collection)->count(), $collection);
        }

        $this->assertFileExists(base_path('content/collections/posts/2020-01-01.hello-world.md'));
        $this->assertDirectoryDoesNotExist(resource_path('content/posts'));
    }

    public function test_native_status_relationships_and_fieldtypes_are_used(): void
    {
        $this->assertSame(
            18,
            Entry::query()->where('collection', 'posts')->whereStatus('published')->get()->count()
        );

        $post = Entry::query()
            ->where('collection', 'posts')
            ->where('slug', 'human-readable-intervals')
            ->first();

        $this->assertInstanceOf(EntryContract::class, $post);
        $this->assertSame('gummibeer', $post->author->id());
        $this->assertContains('laravel', $post->categories->map(fn ($term) => $term->slug())->all());

        $job = Entry::query()
            ->where('collection', 'jobs')
            ->where('slug', 'hospitable')
            ->first();

        $this->assertInstanceOf(EntryContract::class, $job);
        $this->assertInstanceOf(CarbonInterface::class, $job->start_at);
    }

    public function test_public_pages_and_statamic_control_panel_boot(): void
    {
        $pages = [
            '/' => null,
            '/resume' => null,
            '/uses' => null,
            '/charity' => 'Sea Shepherd',
            '/portfolio' => 'Moin Hund',
            '/imprint' => null,
            '/privacy' => null,
            '/cp/auth/login' => null,
        ];

        foreach ($pages as $path => $expectedContent) {
            $response = $this->get($path)->assertOk();

            if ($expectedContent !== null) {
                $response->assertSee($expectedContent);
            }
        }

        $this->getJson('/blog/search.json')
            ->assertOk()
            ->assertJsonCount(18);
    }

    public function test_migrated_images_are_private_statamic_assets_above_webroot(): void
    {
        $container = AssetContainer::findByHandle('images');

        $this->assertNotNull($container);
        $this->assertSame('images', $container->handle());
        $this->assertTrue($container->private());
        $this->assertSame(resource_path('images'), config('filesystems.disks.images.root'));
        $this->assertSame('private', config('filesystems.disks.images.visibility'));
        $this->assertArrayNotHasKey('url', config('filesystems.disks.images'));
        $this->assertDirectoryDoesNotExist(public_path('images'));

        foreach ([
            'charity/seashepherd.png',
            'company/hospitable.png',
            'favicons/favicon.ico',
            'hacktoberfest/2023.png',
            'og/static/home.png',
            'portfolio/moinhund.png',
            'posts/2020-01-01.hello-world.jpg',
            'posts/2021-01-28.yoda/content-paw-prints.jpg',
        ] as $image) {
            $this->assertFileExists(resource_path('images/'.$image), $image);
        }

        $this->assertNotNull(Asset::find('images::posts/2020-01-01.hello-world.jpg'));
        $this->get('/images/posts/2020-01-01.hello-world.jpg')->assertNotFound();
    }

    public function test_private_images_only_emit_signed_statamic_glide_urls(): void
    {
        $image = app()->make(Img::class, [
            'src' => 'images/posts/2020-01-01.hello-world.jpg',
            'width' => 400,
            'height' => 250,
            'crop' => true,
        ]);
        $src = html_entity_decode($image->src());

        $this->assertStringContainsString('/img/asset/', $src);
        $this->assertStringContainsString('fit=smartcrop', $src);
        $this->assertStringContainsString('s=', $src);
        $this->assertStringNotContainsString('/images/posts/', $src);

        $meta = new MetaBag;
        $meta->image = asset('images/og/static/home.png');
        $metaImage = html_entity_decode($meta->image);

        $this->assertStringContainsString('/img/asset/', $metaImage);
        $this->assertStringContainsString('s=', $metaImage);
        $this->assertStringNotContainsString('/images/og/', $metaImage);

        $favicons = html_entity_decode(view('components.favicons')->render());
        $this->assertStringContainsString('/img/asset/', $favicons);
        $this->assertStringContainsString('s=', $favicons);
        $this->assertStringNotContainsString('/images/favicons/', $favicons);
    }

    public function test_figure_captions_use_statamic_markdown(): void
    {
        $html = view('components.figure', [
            'attributes' => new ComponentAttributeBag,
            'slot' => new ComponentSlot('Image'),
            'caption' => new ComponentSlot('**Bold caption**'),
        ])->render();

        $this->assertStringContainsString('<strong>Bold caption</strong>', $html);
    }

    public function test_tight_markdown_lists_do_not_wrap_items_in_paragraphs(): void
    {
        $html = Markdown::parse(<<<'MD'
- **[Node](https://wiki.openstreetmap.org/wiki/Node):** a specific point on the earth's surface
- **[Way](https://wiki.openstreetmap.org/wiki/Way):** a linear feature or boundary or an area
- **[Relation](https://wiki.openstreetmap.org/wiki/Relation):** a combination of nodes and or ways
MD);

        $this->assertStringContainsString('<li><strong><a href="https://wiki.openstreetmap.org/wiki/Node">Node</a>:</strong>', $html);
        $this->assertStringNotContainsString('<li><p>', $html);
    }

    public function test_statamic_glide_uses_pixpipe_smartcrop(): void
    {
        $manipulators = array_map(
            static fn ($manipulator): string => $manipulator::class,
            app(Server::class)->getApi()->getManipulators(),
        );

        $this->assertContains(PixpipeSize::class, $manipulators);
        $this->assertNotContains(Size::class, $manipulators);
    }
}
