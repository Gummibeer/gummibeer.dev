<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            'fa.gummibeer.dev/*' => Http::response(
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M0 0"/></svg>',
                200,
                ['Content-Type' => 'image/svg+xml'],
            ),
        ]);
    }
}
