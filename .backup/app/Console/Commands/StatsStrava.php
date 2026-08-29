<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class StatsStrava extends Command
{
    protected $name = 'stats:strava';

    protected $description = 'Load strava statistics.';

    public function handle()
    {
        $url = sprintf(
            'https://www.strava.com/api/v3/oauth/token?grant_type=refresh_token&refresh_token=%s&client_id=%s&client_secret=%s',
            config('services.strava.refresh_token'),
            config('services.strava.client_id'),
            config('services.strava.client_secret')
        );

        $token = Http::post($url)->json()['access_token'];

        $url = sprintf('https://www.strava.com/api/v3/athletes/%s/stats', config('services.strava.athlete_id'));

        $data = Http::withHeaders(['Authorization' => 'Bearer '.$token])->get($url)->json()['all_ride_totals'];

        $this->line(sprintf(
            '[<info>%s</info>] count: <comment>%d</comment> | distance: <comment>%d</comment>km | elevation: <comment>%d</comment>m | time: <comment>%d</comment>h',
            config('services.strava.athlete_id'),
            $data['count'],
            $data['distance'] / 1000,
            $data['elevation_gain'],
            $data['moving_time'] / 60 / 60
        ));

        file_put_contents(resource_path(sprintf('content/strava/%s.json', config('services.strava.athlete_id'))), json_encode($data));
    }
}
