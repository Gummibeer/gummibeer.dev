<x-strava.card
    icon="fa-mountains"
    label="Elevation"
    :value="app(App\Services\StravaStats::class)->elevationGain()"
    unit="m"
/>
