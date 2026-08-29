<?php

namespace Tests\Feature;

use App\Imaging\PixpipeGlideManager;
use Astrotomic\Pixpipe\Manipulators\Size as PixpipeSize;
use League\Glide\Server;
use Statamic\Contracts\Imaging\ImageManipulator;
use Statamic\Facades\Antlers;
use Statamic\Facades\Glide;
use Statamic\Imaging\GlideImageManipulator;
use Statamic\Imaging\GlideManager;
use Tests\TestCase;

class StatamicPixpipeTest extends TestCase
{
    public function test_default_statamic_imaging_contract_uses_pixpipe(): void
    {
        $this->assertInstanceOf(GlideImageManipulator::class, app(ImageManipulator::class));
        $this->assertInstanceOf(PixpipeGlideManager::class, app(GlideManager::class));

        $this->assertUsesPixpipe(app(Server::class));
        $this->assertUsesPixpipe(Glide::server());
    }

    private function assertUsesPixpipe(Server $server): void
    {
        $manipulators = $server->getApi()->getManipulators();

        $this->assertCount(
            1,
            array_filter($manipulators, fn (object $manipulator): bool => $manipulator instanceof PixpipeSize),
        );
    }

    public function test_glide_tag_generates_and_serves_a_signed_smartcrop_url(): void
    {
        $url = trim((string) Antlers::parse(
            '{{ glide src="/assets/redesign/bike-hero.jpg" width="160" height="90" fit="smartcrop" }}',
            [],
            true,
        ));

        $this->assertMatchesRegularExpression(
            '#^/img/asset/[A-Za-z0-9_-]+/bike-hero\.jpg\?#',
            $url,
        );

        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        $this->assertSame('160', $query['w']);
        $this->assertSame('90', $query['h']);
        $this->assertSame('smartcrop', $query['fit']);
        $this->assertNotEmpty($query['s']);

        $response = $this->get($url)->assertOk();
        $dimensions = getimagesizefromstring($response->streamedContent());

        $this->assertSame([160, 90], array_slice($dimensions, 0, 2));
    }

    public function test_tampered_glide_manipulations_are_rejected(): void
    {
        $url = trim((string) Antlers::parse(
            '{{ glide src="/assets/redesign/bike-hero.jpg" width="160" height="90" fit="smartcrop" }}',
            [],
            true,
        ));

        $this->get(str_replace('w=160', 'w=161', $url))->assertBadRequest();
    }
}
