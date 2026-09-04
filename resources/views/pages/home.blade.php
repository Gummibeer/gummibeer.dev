@extends ('web')

@push ('head')
    <x-og.website />
@endpush

@section ('content')
    <x-article class="prose md:prose-lg lg:prose-xl"> {!! $content !!} </x-article>

    <x-home.posts />
    <x-home.streams />

    <x-section class="relative overflow-hidden">
        <img
            src="{{ route('assets.tire') }}"
            alt=""
            aria-hidden="true"
            loading="lazy"
            class="absolute bottom-0 left-0 -z-10 hidden max-h-full opacity-10 md:block"
        />

        <div class="mx-auto w-full space-y-8 sm:max-w-screen-sm sm:px-4 md:max-w-screen-md md:px-0">
            <div class="prose md:prose-lg lg:prose-xl">
                <h2>Biking</h2>
                <p>As a compensation to my job sitting at a desk all day long and starring on a screen - I try to ride as much bike as possible.</p>
                <p>Most of the time I'm riding my mountainbike - even if I live in Hamburg and we have no mountains. I feel comfortable in the saddle, the steering and wheels provides good control on every ground and with an enormous bandwidth of gears I can keep my cadence.</p>
                <p>On top of my daily rides to work, grocery and so on - I'm doing at least one long-trip per year. And since 2019 I'm training for the triathlon bike part and also a 24h-bike-race.</p>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 md:gap-8 lg:gap-10 xl:gap-12">
                <x-strava.card
                    icon="ski-clock"
                    label="Distance"
                    :value="$strava->distance / 1000"
                    unit="km"
                />
                <x-strava.card
                    icon="ski-mountain"
                    label="Elevation"
                    :value="$strava->elevation_gain"
                    unit="m"
                />
                <x-strava.card
                    icon="ski-timer"
                    label="Time"
                    :value="$strava->moving_time / 60 / 60"
                    unit="h"
                />
            </div>

            <ul class="grid list-none grid-cols-4 gap-4 text-center text-4xl sm:grid-cols-8 md:grid-cols-10 lg:grid-cols-12">
                @foreach (collect(['BG', 'CZ', 'DE', 'ES', 'FR', 'PL', 'PT', 'BE', 'NL', 'DK', 'LU', 'AT', 'CH', 'IT', 'GB'])->sort() as $country)
                    <li>
                        <x-twemoji> {{ \Spatie\Emoji\Emoji::countryFlag($country) }} </x-twemoji>
                    </li>
                @endforeach
            </ul>
        </div>
    </x-section>
@endsection
