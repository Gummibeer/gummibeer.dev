<?php

namespace Tests\Feature;

use PHPUnit\Framework\Assert;
use Statamic\Facades\GlobalSet;
use Tests\TestCase;

final class GlobalStravaTest extends TestCase
{
    public function test_strava_stats_are_managed_by_the_native_statamic_global(): void
    {
        $strava = GlobalSet::findByHandle('strava');

        Assert::assertNotNull($strava);
        Assert::assertFileExists(base_path('content/globals/strava.yaml'));
        Assert::assertFileExists(base_path('content/globals/default/strava.yaml'));
        Assert::assertFileExists(resource_path('blueprints/globals/strava.yaml'));
        Assert::assertFalse(is_dir(resource_path('content/strava')));

        $variables = $strava->inDefaultSite();

        Assert::assertSame(0, $variables->get('distance'));
        Assert::assertSame(0, $variables->get('elevation_gain'));
        Assert::assertSame(0, $variables->get('moving_time'));
    }
}
