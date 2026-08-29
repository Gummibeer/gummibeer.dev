<?php /** @var Statamic\Contracts\Entries\Entry $me */ ?>
<?php /** @var Illuminate\Support\Collection $posts */ ?>
<?php /** @var Illuminate\Support\Collection $streams */ ?>

@extends ('web')

@section ('content')
    <x-article class="prose md:prose-lg lg:prose-xl"> {!! $me->content !!} </x-article>

    @if ($posts->isNotEmpty())
        <x-section>
            <x-post.promo :post="$posts->first()" />
        </x-section>
    @endif

    @if ($posts->skip(1)->isNotEmpty())
        <x-section class="bg-dotted">
            <h2 class="mb-8 text-4xl leading-none font-bold text-night-0 dark:text-white">Latest Posts</h2>
            <x-grid>
                @foreach ($posts->skip(1)->take(3) as $post)
                    <x-post.preview
                        :post="$post"
                        :class="$loop->iteration === 3 ? 'hidden lg:block' : ''"
                    />
                @endforeach
            </x-grid>
        </x-section>
    @endif

    <x-section>
        <h2 class="mb-8 text-4xl leading-none font-bold text-night-0 dark:text-white">Latest Streams</h2>
        <x-grid>
            @foreach ($streams->take(3) as $stream)
                <x-stream.preview :stream="$stream" />
            @endforeach
        </x-grid>
    </x-section>

    <x-section class="relative overflow-hidden">
        <x-svg.tire class="absolute bottom-0 left-0 -z-10 hidden max-h-full opacity-10 md:block" />

        <div class="mx-auto w-full space-y-8 sm:max-w-screen-sm sm:px-4 md:max-w-screen-md md:px-0">
            <div class="prose md:prose-lg lg:prose-xl">
                <h2>Biking</h2>
                <p>As a compensation to my job sitting at a desk all day long and starring on a screen - I try to ride as much bike as possible.</p>
                <p>Most of the time I'm riding my mountainbike - even if I live in Hamburg and we have no mountains. I feel comfortable in the saddle, the steering and wheels provides good control on every ground and with an enormous bandwidth of gears I can keep my cadence.</p>
                <p>On top of my daily rides to work, grocery and so on - I'm doing at least one long-trip per year. And since 2019 I'm training for the triathlon bike part and also a 24h-bike-race.</p>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 md:gap-8 lg:gap-10 xl:gap-12">
                <x-strava.distance />
                <x-strava.elevation />
                <x-strava.time />
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
