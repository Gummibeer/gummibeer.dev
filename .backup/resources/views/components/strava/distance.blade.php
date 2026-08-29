<x-strava.card
    icon="fa-route"
    label="Distance"
    :value="sheets('strava')->get(config('services.strava.athlete_id'))->distance / 1000"
    unit="km"
/>