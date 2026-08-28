<x-strava.card
    icon="fa-stopwatch"
    label="Time"
    :value="app(App\Services\StravaStats::class)->movingTime() / 60 / 60"
    unit="h"
/>
