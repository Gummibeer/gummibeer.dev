<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        file_put_contents(public_path('mix-manifest.json'), json_encode([
            '/css/app.css' => '/css/app.css',
            '/js/app.js' => '/js/app.js',
            '/images/og/static/home.png' => '/images/og/static/home.png',
            '/images/og/static/blog.png' => '/images/og/static/blog.png',
            '/images/og/static/me.png' => '/images/og/static/me.png',
            '/images/og/static/uses.png' => '/images/og/static/uses.png',
            '/images/og/static/charity.png' => '/images/og/static/charity.png',
            '/images/og/static/portfolio.png' => '/images/og/static/portfolio.png',
        ], JSON_THROW_ON_ERROR));
    }

    protected function tearDown(): void
    {
        @unlink(public_path('mix-manifest.json'));

        parent::tearDown();
    }
}
