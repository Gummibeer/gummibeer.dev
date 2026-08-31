<?php

namespace Tests\Feature;

use App\View\Components\Img;
use Astrotomic\Pixpipe\Manipulators\Size as PixpipeSize;
use Carbon\CarbonInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;
use League\Glide\Manipulators\Size;
use League\Glide\Server;
use PHPUnit\Framework\Assert;
use Statamic\Auth\File\User as FileUser;
use Statamic\Contracts\Entries\Entry as EntryContract;
use Statamic\Facades\Asset;
use Statamic\Facades\AssetContainer;
use Statamic\Facades\Entry;
use Statamic\Facades\Markdown;
use Statamic\Facades\User;
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
        ];

        foreach ($expected as $collection => $count) {
            Assert::assertSame($count, Entry::whereCollection($collection)->count(), $collection);
        }

        Assert::assertFileExists(base_path('content/collections/posts/2020-01-01.hello-world.md'));
        Assert::assertFileDoesNotExist(base_path('content/collections/authors.yaml'));
        Assert::assertDirectoryDoesNotExist(base_path('content/collections/drafts'));
        Assert::assertDirectoryDoesNotExist(resource_path('content/posts'));

        $author = User::find('gummibeer');
        Assert::assertInstanceOf(FileUser::class, $author);
        Assert::assertSame('dev@gummibeer.de', $author->email());
        Assert::assertSame('gummibeer', $author->get('slug'));
        Assert::assertSame('Gummibeer', $author->get('nickname'));
    }

    public function test_native_status_relationships_and_fieldtypes_are_used(): void
    {
        Assert::assertSame(
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

            Assert::assertInstanceOf(EntryContract::class, $draft, $slug);
            Assert::assertSame('posts', $draft->collection()->handle(), $slug);
            Assert::assertSame('draft', $draft->status(), $slug);
        }

        $post = Entry::query()
            ->where('collection', 'posts')
            ->where('slug', 'human-readable-intervals')
            ->first();

        Assert::assertInstanceOf(EntryContract::class, $post);
        Assert::assertInstanceOf(FileUser::class, $post->author);
        Assert::assertSame('gummibeer', $post->author->id());
        Assert::assertSame('dev@gummibeer.de', $post->author->email());
        Assert::assertContains('laravel', $post->categories->map(fn ($term) => $term->slug())->all());

        $job = Entry::query()
            ->where('collection', 'jobs')
            ->where('slug', 'hospitable')
            ->first();

        Assert::assertInstanceOf(EntryContract::class, $job);
        Assert::assertInstanceOf(CarbonInterface::class, $job->start_at);
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

        Assert::assertInstanceOf(EntryContract::class, $home);
        Assert::assertInstanceOf(EntryContract::class, $blog);
        Assert::assertInstanceOf(EntryContract::class, $post);
        Assert::assertInstanceOf(EntryContract::class, $categorizedPost);
        Assert::assertSame('/', $home->url());
        Assert::assertSame('/blog', $blog->url());
        Assert::assertSame('/blog/2020/hello-world', $post->url());
        Assert::assertSame('/blog/categories/laravel', $categorizedPost->categories->firstWhere('slug', 'laravel')?->url());
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
        Assert::assertSame(['collection:posts'], config('statamic.search.indexes.blog.searchables'));
        Assert::assertSame(
            ['title', 'description', 'categories', 'content'],
            config('statamic.search.indexes.blog.fields')
        );

        Assert::assertSame(0, Artisan::call('statamic:search:update', ['index' => 'blog']));

        $this->get('/blog')
            ->assertOk()
            ->assertSee('action="'.route('blog.search').'"', false);

        $this->get('/blog/search?q=OpenStreetMap')
            ->assertOk()
            ->assertSee('Geography in Laravel: retrieving geographical data');

        $this->get('/blog/search?q=thereisabsolutelynopostmatchingthis')
            ->assertOk()
            ->assertSee('No posts found');

        $this->get('/blog/search.json')
            ->assertStatus(301)
            ->assertRedirect('/blog/search');
    }

    public function test_sitemap_generation_works_with_spatie_sitemap_v8(): void
    {
        config()->set('sitemap.guzzle_options.handler', new MockHandler([
            new GuzzleResponse(200, ['Content-Type' => 'text/plain'], ''),
            new GuzzleResponse(200, ['Content-Type' => 'text/html'], '<html><body>Home</body></html>'),
        ]));

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('<urlset', false)
            ->assertSee('<loc>http://localhost</loc>', false);
    }

    public function test_migrated_images_are_private_statamic_assets_above_webroot(): void
    {
        $container = AssetContainer::findByHandle('images');

        Assert::assertNotNull($container);
        Assert::assertSame('images', $container->handle());
        Assert::assertTrue($container->private());
        Assert::assertSame(resource_path('images'), config('filesystems.disks.images.root'));
        Assert::assertSame('private', config('filesystems.disks.images.visibility'));
        Assert::assertArrayNotHasKey('url', config('filesystems.disks.images'));
        Assert::assertDirectoryDoesNotExist(public_path('images'));

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
            Assert::assertFileExists(resource_path('images/'.$image), $image);
        }

        Assert::assertNotNull(Asset::find('images::posts/2020-01-01.hello-world.jpg'));
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

        Assert::assertStringContainsString('/img/asset/', $src);
        Assert::assertStringContainsString('fit=smartcrop', $src);
        Assert::assertStringContainsString('s=', $src);
        Assert::assertStringNotContainsString('/images/posts/', $src);

        $webpPath = (string) parse_url($webpSrc, PHP_URL_PATH);
        $webpQuery = (string) parse_url($webpSrc, PHP_URL_QUERY);

        $this->get($webpPath.'?'.$webpQuery)
            ->assertOk()
            ->assertHeader('content-type', 'image/webp');

        $favicons = html_entity_decode(view('components.favicons')->render());
        Assert::assertStringContainsString('/img/asset/', $favicons);
        Assert::assertStringContainsString('s=', $favicons);
        Assert::assertStringNotContainsString('/images/favicons/', $favicons);
    }

    public function test_remote_images_use_signed_statamic_glide_without_a_public_source_cache(): void
    {
        $image = app()->make(Img::class, [
            'src' => 'https://i.ytimg.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
            'width' => 400,
        ]);
        $src = html_entity_decode($image->src('webp'));

        Assert::assertStringContainsString('/img/http/', $src);
        Assert::assertStringContainsString('fm=webp', $src);
        Assert::assertStringContainsString('s=', $src);
        Assert::assertStringNotContainsString('/vendor/images/', $src);
        Assert::assertDirectoryDoesNotExist(public_path('vendor/images'));
    }

    public function test_figure_captions_use_statamic_markdown(): void
    {
        $html = view('components.figure', [
            'attributes' => new ComponentAttributeBag,
            'slot' => new ComponentSlot('Image'),
            'caption' => new ComponentSlot('**Bold caption**'),
        ])->render();

        Assert::assertStringContainsString('<strong>Bold caption</strong>', $html);
    }

    public function test_tight_markdown_lists_do_not_wrap_items_in_paragraphs(): void
    {
        $html = Markdown::parse(<<<'MD'
- **[Node](https://wiki.openstreetmap.org/wiki/Node):** a specific point on the earth's surface
- **[Way](https://wiki.openstreetmap.org/wiki/Way):** a linear feature or boundary or an area
- **[Relation](https://wiki.openstreetmap.org/wiki/Relation):** a combination of nodes and or ways
MD);

        Assert::assertStringContainsString('<li><strong><a href="https://wiki.openstreetmap.org/wiki/Node">Node</a>:</strong>', $html);
        Assert::assertStringNotContainsString('<li><p>', $html);
    }

    public function test_statamic_glide_uses_pixpipe_smartcrop(): void
    {
        $manipulators = array_map(
            static fn ($manipulator): string => $manipulator::class,
            app(Server::class)->getApi()->getManipulators(),
        );

        Assert::assertContains(PixpipeSize::class, $manipulators);
        Assert::assertNotContains(Size::class, $manipulators);
    }
}
