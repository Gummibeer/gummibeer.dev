<?php

namespace Tests\Feature;

use Astrotomic\GithubSponsors\Graphql;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Statamic\Facades\Antlers;
use Tests\TestCase;

class GithubSponsorsTagTest extends TestCase
{
    public function test_it_exposes_live_sponsors_and_caches_the_response(): void
    {
        config()->set('services.github.sponsors_token', 'test-token');
        app()->forgetInstance(Graphql::class);
        $this->forgetSponsorsCache();

        Http::fake([
            'api.github.com/graphql' => Http::response([
                'data' => [
                    'user' => [
                        'sponsorshipsAsMaintainer' => [
                            'pageInfo' => ['hasNextPage' => false, 'endCursor' => null],
                            'nodes' => [
                                ['sponsorEntity' => [
                                    '__typename' => 'User',
                                    'login' => 'octocat',
                                    'name' => 'The Octocat',
                                    'avatarUrl' => 'https://avatars.example/octocat.png',
                                    'url' => 'https://github.com/octocat',
                                ]],
                            ],
                        ],
                    ],
                    'organization' => null,
                ],
            ]),
        ]);

        $template = <<<'ANTLERS'
            {{ github_sponsors login="Gummibeer" }}
                {{ count_formatted }}|{{ sponsors }}{{ login }}|{{ name }}|{{ avatar_url }}{{ /sponsors }}
            {{ /github_sponsors }}
            ANTLERS;

        $first = Antlers::parse($template);
        $second = Antlers::parse($template);

        $this->assertStringContainsString('1|octocat|TheOctocat|https://avatars.example/octocat.png', preg_replace('/\s+/', '', $first));
        $this->assertSame(preg_replace('/\s+/', '', $first), preg_replace('/\s+/', '', $second));
        Http::assertSentCount(1);
    }

    public function test_it_uses_cms_fallback_without_a_token(): void
    {
        config()->set('services.github.sponsors_token', '');
        $this->forgetSponsorsCache();
        Http::fake();

        $html = Antlers::parse(<<<'ANTLERS'
            {{ github_sponsors login="Gummibeer" fallback_count="12" }}
                {{ count_formatted }}|{{ if live }}live{{ else }}fallback{{ /if }}
            {{ /github_sponsors }}
            ANTLERS);

        $this->assertStringContainsString('12|fallback', preg_replace('/\s+/', '', $html));
        Http::assertNothingSent();
    }

    private function forgetSponsorsCache(): void
    {
        $key = 'github-sponsors.'.hash('sha256', 'gummibeer');

        Cache::forget($key);
        Cache::forget('illuminate:cache:flexible:created:'.$key);
    }
}
