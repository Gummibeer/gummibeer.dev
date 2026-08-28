<?php

namespace Tests\Feature;

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
            ->assertJsonCount(19);
    }
}
