<x-strava.card
    icon="fa-stopwatch"
    label="Time"
    :value="sheets('strava')->get(config('services.strava.athlete_id'))->moving_time / 60 / 60"
    unit="h"
/>