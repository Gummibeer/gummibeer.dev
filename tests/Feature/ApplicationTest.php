<?php

namespace Tests\Feature;

use App\Services\OgImage;
use App\View\Components\Img;
use Astrotomic\Pixpipe\Manipulators\Size as PixpipeSize;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;
use League\Glide\Manipulators\Size;
use League\Glide\Server;
use Statamic\Contracts\Assets\Asset as AssetContract;
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
            'posts' => 29,
            'streams' => 13,
            'jobs' => 9,
            'hacktoberfest' => 6,
            'pages' => 8,
            'authors' => 1,
        ];

        foreach ($expected as $collection => $count) {
            $this->assertSame($count, Entry::whereCollection($collection)->count(), $collection);
        }

        $this->assertFileExists(base_path('content/collections/posts/2020-01-01.hello-world.md'));
        $this->assertDirectoryDoesNotExist(base_path('content/collections/drafts'));
        $this->assertDirectoryDoesNotExist(resource_path('content/posts'));
    }

    public function test_native_status_relationships_and_fieldtypes_are_used(): void
    {
        $this->assertSame(
            18,
            Entry::query()->where('collection', 'posts')->whereStatus('published')->get()->count()
        );

        foreach ([
            'alpinejs-responsive-xcloak',
            'blade-components',
            'composite-rules',
            'custom-url-generator',
            'developer-courtesy',
            'imgix-cloudflare',
            'laravel-translate-array',
            'loading-pivot-data',
            'phpunit-laravel-mix',
            'validate-change-password',
        ] as $slug) {
            $draft = Entry::query()
                ->where('collection', 'posts')
                ->where('slug', $slug)
                ->first();

            $this->assertInstanceOf(EntryContract::class, $draft, $slug);
            $this->assertSame('posts', $draft->collection()->handle(), $slug);
            $this->assertSame('draft', $draft->status(), $slug);
        }

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

    public function test_public_content_owns_native_statamic_urls(): void
    {
        $home = Entry::find('80ca56e0-263a-4baf-9716-07089aebc322');
        $blog = Entry::find('ff99d13f-f5d7-4dd7-8d71-60aeb0f1bd43');
        $post = Entry::query()
            ->where('collection', 'posts')
            ->where('slug', 'hello-world')
            ->first();
        $categorizedPost = Entry::query()
            ->where('collection', 'posts')
            ->where('slug', 'human-readable-intervals')
            ->first();

        $this->assertInstanceOf(EntryContract::class, $home);
        $this->assertInstanceOf(EntryContract::class, $blog);
        $this->assertInstanceOf(EntryContract::class, $post);
        $this->assertInstanceOf(EntryContract::class, $categorizedPost);
        $this->assertSame('/', $home->url());
        $this->assertSame('/blog', $blog->url());
        $this->assertSame('/blog/2020/hello-world', $post->url());
        $this->assertSame('/blog/categories/laravel', $categorizedPost->categories->firstWhere('slug', 'laravel')?->url());
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
            '/blog/2020/hello-world' => 'Hello World',
            '/cp/auth/login' => null,
        ];

        foreach ($pages as $path => $expectedContent) {
            $response = $this->get($path)->assertOk();

            if ($expectedContent !== null) {
                $response->assertSee($expectedContent);
            }
        }
    }

    public function test_blog_search_uses_the_native_statamic_index(): void
    {
        $this->assertSame(['collection:posts'], config('statamic.search.indexes.blog.searchables'));
        $this->assertSame(
            ['title', 'description', 'categories', 'content'],
            config('statamic.search.indexes.blog.fields')
        );

        $this->assertSame(0, Artisan::call('statamic:search:update', ['index' => 'blog']));

        $this->get('/blog')
            ->assertOk()
            ->assertSee('action="'.route('blog.search').'"', false);

        $this->get('/blog/search?q=OpenStreetMap')
            ->assertOk()
            ->assertSee('Geography in Laravel: retrieving geographical data');

        $this->get('/blog/search?q=thereisabsolutelynopostmatchingthis')
            ->assertOk()
            ->assertSee('No posts found');

        $this->get('/blog/search.json')->assertNotFound();
    }

    public function test_seo_pro_derives_metadata_from_statamic_content(): void
    {
        $home = $this->get('/')->assertOk();
        $home
            ->assertSee('<title>Tom Herrmann | Gummibeer</title>', false)
            ->assertSee('<meta property="og:type" content="website" />', false)
            ->assertSee('<link href="http://localhost" rel="canonical" />', false)
            ->assertSee('<meta property="og:image"', false);

        $post = $this->get('/blog/2020/hello-world')->assertOk();
        $post
            ->assertSee('<title>Hello World | Blog | Gummibeer</title>', false)
            ->assertSee('<meta property="og:type" content="article" />', false)
            ->assertSee('http://localhost/blog/2020/hello-world', false)
            ->assertSee('<meta property="og:image"', false);
    }

    public function test_custom_blog_routes_feed_derived_context_into_seo_pro(): void
    {
        $this->get('/blog')
            ->assertOk()
            ->assertSee('<title>Blog | Gummibeer</title>', false)
            ->assertSee('<meta property="og:image"', false);

        $this->get('/blog/2020')
            ->assertOk()
            ->assertSee('<title>Posts from 2020 | Blog | Gummibeer</title>', false);

        $this->get('/blog/search?q=OpenStreetMap')
            ->assertOk()
            ->assertSee('<title>Search: OpenStreetMap | Gummibeer</title>', false)
            ->assertSee('content="noindex"', false);
    }

    public function test_sitemap_generation_is_owned_by_seo_pro(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('<urlset', false)
            ->assertSee('http://localhost/blog/2020/hello-world', false);
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

    public function test_private_images_only_emit_and_serve_signed_statamic_glide_urls(): void
    {
        $image = app()->make(Img::class, [
            'src' => 'images/posts/2020-01-01.hello-world.jpg',
            'width' => 400,
            'height' => 250,
            'crop' => true,
        ]);
        $src = html_entity_decode($image->src());
        $webpSrc = html_entity_decode($image->src('webp'));

        $this->assertStringContainsString('/img/asset/', $src);
        $this->assertStringContainsString('fit=smartcrop', $src);
        $this->assertStringContainsString('s=', $src);
        $this->assertStringNotContainsString('/images/posts/', $src);

        $webpPath = (string) parse_url($webpSrc, PHP_URL_PATH);
        $webpQuery = (string) parse_url($webpSrc, PHP_URL_QUERY);

        $this->get($webpPath.'?'.$webpQuery)
            ->assertOk()
            ->assertHeader('content-type', 'image/webp');

        $home = Entry::find('80ca56e0-263a-4baf-9716-07089aebc322');
        $this->assertInstanceOf(EntryContract::class, $home);
        $this->assertInstanceOf(AssetContract::class, app(OgImage::class)->forPage($home));

        $metadata = html_entity_decode($this->get('/')->assertOk()->getContent());
        $this->assertStringContainsString('<meta property="og:image"', $metadata);
        $this->assertStringNotContainsString('/images/og/', $metadata);

        $favicons = html_entity_decode(view('components.favicons')->render());
        $this->assertStringContainsString('/img/asset/', $favicons);
        $this->assertStringContainsString('s=', $favicons);
        $this->assertStringNotContainsString('/images/favicons/', $favicons);
    }

    public function test_remote_images_use_signed_statamic_glide_without_a_public_source_cache(): void
    {
        $image = app()->make(Img::class, [
            'src' => 'https://i.ytimg.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
            'width' => 400,
        ]);
        $src = html_entity_decode($image->src('webp'));

        $this->assertStringContainsString('/img/http/', $src);
        $this->assertStringContainsString('fm=webp', $src);
        $this->assertStringContainsString('s=', $src);
        $this->assertStringNotContainsString('/vendor/images/', $src);
        $this->assertDirectoryDoesNotExist(public_path('vendor/images'));
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
