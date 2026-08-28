<?php

namespace Tests\Feature;

use App\View\Components\Img;
use Astrotomic\Pixpipe\Manipulators\Size as PixpipeSize;
use Carbon\CarbonInterface;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\ComponentSlot;
use League\Glide\Manipulators\Size;
use League\Glide\Server;
use Statamic\Contracts\Entries\Entry as EntryContract;
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
        foreach (['/', '/resume', '/privacy', '/cp/auth/login'] as $path) {
            $this->get($path)->assertOk();
        }

        $this->getJson('/blog/search.json')
            ->assertOk()
            ->assertJsonCount(18);
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

        $image = app()->make(Img::class, [
            'src' => 'images/example.jpg',
            'width' => 400,
            'height' => 250,
            'crop' => true,
        ]);

        $this->assertStringContainsString('fit=smartcrop', $image->src());
    }
}
