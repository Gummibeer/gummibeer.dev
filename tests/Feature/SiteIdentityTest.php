<?php

namespace Tests\Feature;

use App\Services\SiteIdentity;
use Tests\TestCase;

final class SiteIdentityTest extends TestCase
{
    public function test_site_identity_is_managed_by_statamic_globals(): void
    {
        $identity = app(SiteIdentity::class);

        $this->assertFileExists(base_path('content/globals/site.yaml'));
        $this->assertFileExists(base_path('content/globals/default/site.yaml'));
        $this->assertFileExists(resource_path('blueprints/globals/site.yaml'));

        $this->assertSame('Gummibeer', $identity->siteName());
        $this->assertSame('Tom Herrmann', $identity->brandName());
        $this->assertSame('Developer / Biker / Gamer', $identity->tagline());
        $this->assertSame('Tom Witkowski', $identity->copyrightName());
        $this->assertSame(2015, $identity->copyrightSince());
        $this->assertSame('tel:+491621525105', $identity->phoneUrl());
        $this->assertSame('mailto:dev@gummibeer.de', $identity->emailUrl());
        $this->assertSame('https://t.me/gummibeer', $identity->telegramUrl());
        $this->assertSame('http://localhost/imprint', $identity->imprintUrl());
        $this->assertSame('http://localhost/privacy', $identity->privacyUrl());
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
}
