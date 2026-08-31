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
}
