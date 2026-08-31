<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Statamic\Facades\GlobalSet;
use Tests\TestCase;

final class GlobalStravaTest extends TestCase
{
    public function test_strava_stats_are_managed_by_the_native_statamic_global(): void
    {
        $strava = GlobalSet::findByHandle('strava');

        $this->assertNotNull($strava);
        $this->assertFileExists(base_path('content/globals/strava.yaml'));
        $this->assertFileExists(base_path('content/globals/default/strava.yaml'));
        $this->assertFileExists(resource_path('blueprints/globals/strava.yaml'));
        $this->assertFalse(is_dir(resource_path('content/strava')));

        $variables = $strava->inDefaultSite();

        $this->assertSame(0, $variables->get('distance'));
        $this->assertSame(0, $variables->get('elevation_gain'));
        $this->assertSame(0, $variables->get('moving_time'));
    }

    public function test_strava_command_updates_the_global(): void
    {
        Http::fake([
            'https://www.strava.com/api/v3/oauth/token*' => Http::response(['access_token' => 'token']),
            'https://www.strava.com/api/v3/athletes/*/stats' => Http::response([
                'all_ride_totals' => [
                    'count' => 42,
                    'distance' => 123456.7,
                    'elevation_gain' => 890.4,
                    'moving_time' => 7200,
                ],
            ]),
        ]);

        try {
            $this->artisan('stats:strava')->assertSuccessful();

            $strava = GlobalSet::findByHandle('strava');
            $this->assertNotNull($strava);

            $variables = $strava->inDefaultSite();
            $this->assertSame(123457, $variables->get('distance'));
            $this->assertSame(890, $variables->get('elevation_gain'));
            $this->assertSame(7200, $variables->get('moving_time'));
        } finally {
            $strava = GlobalSet::findByHandle('strava');

            if ($strava) {
                $strava->inDefaultSite()->data([
                    'distance' => 0,
                    'elevation_gain' => 0,
                    'moving_time' => 0,
                ])->save();
            }
        }
    }
}
