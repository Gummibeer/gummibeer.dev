<x-strava.card
    icon="fa-route"
    label="Distance"
    :value="app(App\Services\StravaStats::class)->distance() / 1000"
    unit="km"
/>
