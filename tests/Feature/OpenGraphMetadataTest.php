<?php

namespace Tests\Feature;

use Tests\TestCase;

class OpenGraphMetadataTest extends TestCase
{
    public function test_homepage_renders_website_and_twitter_metadata(): void
    {
        $response = $this->get('/')
            ->assertSuccessful()
            ->assertSee('<meta property="og:type" content="website">', false)
            ->assertSee('<meta property="og:title" content="gummibeer.dev — building, shipping, learning, sharing">', false)
            ->assertSee('<meta property="og:site_name" content="gummibeer.dev">', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
            ->assertSee('<meta name="twitter:site" content="@Gummibeer">', false);

        $this->assertMatchesRegularExpression(
            '/<meta property="og:image" content="https?:\/\/[^\"]+\/assets\/redesign\/bike-hero\.jpg">/',
            $response->getContent(),
        );
    }

    public function test_article_renders_article_specific_metadata(): void
    {
        $response = $this->get('/blog/the-power-of-shipping-something-small')
            ->assertSuccessful()
            ->assertSee('<meta property="og:type" content="article">', false)
            ->assertSee('<meta property="og:title" content="The power of shipping something small">', false)
            ->assertSee('<meta property="article:section" content="Process">', false)
            ->assertSee('<meta property="article:tag" content="Side projects">', false)
            ->assertSee('<meta property="article:author" content="https://github.com/Gummibeer">', false)
            ->assertSee('<meta name="twitter:creator" content="@Gummibeer">', false);

        $this->assertMatchesRegularExpression(
            '/<meta property="og:image" content="https?:\/\/[^\"]+\/assets\/redesign\/article-ship\.png">/',
            $response->getContent(),
        );
    }
}
