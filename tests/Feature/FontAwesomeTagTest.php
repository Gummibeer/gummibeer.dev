<?php

namespace Tests\Feature;

use App\Tags\FontAwesome;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Statamic\Facades\Antlers;
use Tests\TestCase;

class FontAwesomeTagTest extends TestCase
{
    public function test_it_fetches_renders_and_caches_an_icon_in_storage(): void
    {
        Storage::fake('local');
        Http::fake([
            'https://fa.gummibeer.dev/v7.2.0/classic/solid/heart.svg' => Http::response(
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M1 2"/></svg>',
                200,
                ['Content-Type' => 'image/svg+xml'],
            ),
        ]);

        $template = '{{ font_awesome set="classic" style="solid" icon="heart" class="size-5 text-red" aria-label="Favorite" }}';
        $first = (string) Antlers::parse($template);
        $second = (string) Antlers::parse($template);

        $this->assertSame($first, $second);
        $this->assertStringContainsString('<svg', $first);
        $this->assertStringContainsString('class="size-5 text-red"', $first);
        $this->assertStringContainsString('aria-label="Favorite"', $first);
        $this->assertStringNotContainsString('aria-hidden=', $first);
        Storage::disk('local')->assertExists('font-awesome/v7.2.0/classic/solid/heart.svg');
        Http::assertSentCount(1);
    }

    public function test_the_default_version_can_be_overridden_per_tag(): void
    {
        Storage::fake('local');
        Http::fake([
            'https://fa.gummibeer.dev/v6.7.2/brands/brands/github.svg' => Http::response(
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512"><path d="M1 2"/></svg>',
                200,
                ['Content-Type' => 'image/svg+xml; charset=utf-8'],
            ),
        ]);

        $html = Antlers::parse('{{ font_awesome version="6.7.2" set="brands" style="brands" icon="github" }}');

        $this->assertStringContainsString('aria-hidden="true"', $html);
        Storage::disk('local')->assertExists('font-awesome/v6.7.2/brands/brands/github.svg');
        Http::assertSent(fn ($request): bool => $request->url() === 'https://fa.gummibeer.dev/v6.7.2/brands/brands/github.svg');
    }

    public function test_the_default_version_is_kept_on_the_tag(): void
    {
        $this->assertSame('7.2.0', FontAwesome::DEFAULT_VERSION);
    }

    public function test_homepage_uses_font_awesome_for_technologies_and_omits_statamic(): void
    {
        Storage::fake('local');

        $response = $this->get('/');

        $response
            ->assertSuccessful()
            ->assertSee('title="PHP"', false)
            ->assertSee('title="Tailwind CSS"', false)
            ->assertDontSee('title="Statamic"', false);

        Http::assertSent(fn ($request): bool => $request->url() === 'https://fa.gummibeer.dev/v7.2.0/brands/brands/laravel.svg');
        Http::assertSentCount(7);
    }

    public function test_it_rejects_invalid_icon_coordinates_before_requesting(): void
    {
        Storage::fake('local');
        Http::fake();

        $this->expectException(InvalidArgumentException::class);

        Antlers::parse('{{ font_awesome set="classic" style="brands" icon="../heart" }}');
    }
}
