<?php

namespace Tests\Feature;

use Astrotomic\Ecologi\Requests\Reporting\GetImpact;
use Illuminate\Support\Facades\Cache;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Tests\TestCase;

class EcologiImpactTest extends TestCase
{
    public function test_homepage_renders_live_ecologi_impact(): void
    {
        $this->forgetImpactCache();

        MockClient::global([
            GetImpact::class => MockResponse::make([
                'trees' => 4321,
                'carbonOffset' => 87.65,
            ]),
        ]);

        $this->get('/')
            ->assertSuccessful()
            ->assertSee('4,321');
    }

    public function test_homepage_uses_cms_fallback_when_ecologi_is_unavailable(): void
    {
        $this->forgetImpactCache();

        MockClient::global([
            GetImpact::class => MockResponse::make([], 503),
        ]);

        $this->get('/')
            ->assertSuccessful()
            ->assertSee('2,050');
    }

    private function forgetImpactCache(): void
    {
        MockClient::destroyGlobal();

        $key = 'ecologi.impact.'.hash('sha256', 'astrotomic');

        Cache::forget($key);
        Cache::forget('illuminate:cache:flexible:created:'.$key);
    }
}
