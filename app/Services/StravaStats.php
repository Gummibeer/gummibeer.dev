<?php

namespace App\Services;

use Illuminate\Support\Arr;

final class StravaStats
{
    /** @var array<string, mixed>|null */
    private ?array $data = null;

    public function distance(): float
    {
        return (float) Arr::get($this->data(), 'distance', 0);
    }

    public function elevationGain(): float
    {
        return (float) Arr::get($this->data(), 'elevation_gain', 0);
    }

    public function movingTime(): float
    {
        return (float) Arr::get($this->data(), 'moving_time', 0);
    }

    /** @return array<string, mixed> */
    private function data(): array
    {
        if ($this->data !== null) {
            return $this->data;
        }

        $path = resource_path(sprintf('content/strava/%s.json', config('services.strava.athlete_id')));

        if (! is_file($path)) {
            return $this->data = [];
        }

        $data = json_decode((string) file_get_contents($path), true);

        return $this->data = is_array($data) ? $data : [];
    }
}
