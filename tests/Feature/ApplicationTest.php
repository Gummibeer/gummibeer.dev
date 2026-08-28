<?php

namespace Tests\Feature;

use App\View\Components\Img;
use Astrotomic\Pixpipe\Manipulators\Size as PixpipeSize;
use League\Glide\Manipulators\Size;
use League\Glide\Server;
use Statamic\Facades\Entry;
use Tests\TestCase;

final class ApplicationTest extends TestCase
{
    public function test_migrated_statamic_collections_are_populated(): void
    {
        $expected = [
            'posts' => 19,
            'drafts' => 10,
            'streams' => 13,
            'jobs' => 9,
            'hacktoberfest' => 6,
            'static' => 7,
        ];

        foreach ($expected as $collection => $count) {
            $this->assertSame($count, Entry::whereCollection($collection)->count(), $collection);
        }
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
