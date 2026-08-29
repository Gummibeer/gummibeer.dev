<x-strava.card
    icon="fa-mountains"
    label="Elevation"
    :value="sheets('strava')->get(config('services.strava.athlete_id'))->elevation_gain"
    unit="m"
/>