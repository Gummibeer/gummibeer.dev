<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Assert;
use Statamic\Facades\GlobalSet;
use Tests\TestCase;

final class GlobalIdentityTest extends TestCase
{
    public function test_identity_is_managed_by_the_native_statamic_global(): void
    {
        $identity = GlobalSet::findByHandle('identity');

        Assert::assertNotNull($identity);
        Assert::assertFileExists(base_path('content/globals/identity.yaml'));
        Assert::assertFileExists(base_path('content/globals/default/identity.yaml'));
        Assert::assertFileExists(resource_path('blueprints/globals/identity.yaml'));
        Assert::assertFileDoesNotExist(base_path('content/globals/site.yaml'));
        Assert::assertFileDoesNotExist(base_path('content/globals/default/site.yaml'));
        Assert::assertFileDoesNotExist(resource_path('blueprints/globals/site.yaml'));

        $variables = $identity->inDefaultSite();

        Assert::assertSame('Gummibeer', $variables->get('site_name'));
        Assert::assertSame('Tom Herrmann', $variables->get('brand_name'));
        Assert::assertSame('Developer / Biker / Gamer', $variables->get('tagline'));
        Assert::assertSame('Tom Witkowski', $variables->get('copyright_name'));
        Assert::assertSame(2015, $variables->get('copyright_since'));
    }

    public function test_site_wide_identity_is_rendered_from_the_global(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Tom Herrmann')
            ->assertSee('Tom Witkowski')
            ->assertSee('https://twitter.com/devgummibeer')
            ->assertSee('https://github.com/Gummibeer')
            ->assertSee('https://instagram.com/dev.gummibeer')
            ->assertSee('tel:+491621525105')
            ->assertSee('mailto:dev@gummibeer.de')
            ->assertSee('https://t.me/gummibeer')
            ->assertSee('http://localhost/imprint')
            ->assertSee('http://localhost/privacy');
    }

    public function test_legacy_identity_abstractions_cannot_be_reintroduced(): void
    {
        $forbidden = [
            'Site'.'Identity',
            'Meta'.'Bag',
        ];

        foreach ([app_path(), resource_path('views'), base_path('tests')] as $directory) {
            foreach (File::allFiles($directory) as $file) {
                if ($file->getRealPath() === __FILE__) {
                    continue;
                }

                $contents = $file->getContents();

                foreach ($forbidden as $name) {
                    Assert::assertStringNotContainsString($name, $contents, $file->getRelativePathname());
                }
            }
        }
    }
}
