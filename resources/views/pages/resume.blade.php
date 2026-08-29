<?php /** @var Illuminate\Support\HtmlString|string $contents */ ?>
<?php /** @var Illuminate\Support\Collection $jobs */ ?>

@extends ('web')

@section ('content')
    <x-article class="prose md:prose-lg lg:prose-xl"> {!! $contents !!} </x-article>

    <x-section class="bg-dotted">
        <div class="mx-auto w-full sm:max-w-screen-sm sm:px-4 md:max-w-screen-md md:px-0">
            <div class="divide-y overflow-hidden rounded-4 bg-white px-4 shadow dark:bg-night-20">
                @foreach ($jobs as $job)
                    <div class="py-4 @if($job->has_end) text-snow-20 dark:text-snow-10 @endif">
                        <div class="flex flex-row sm:items-center sm:space-x-4">
                            @if ($job->logo)
                                <div class="hidden h-24 w-24 sm:block">
                                    <x-img
                                        :src="$job->logo"
                                        :alt="$job->name"
                                        class="h-full w-full object-contain"
                                    />
                                </div>
                            @endif
                            <div class="grow">
                                <div class="flex flex-col items-center justify-between sm:flex-row">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4">
                                        <h3 class="text-2xl @if(!$job->has_end) font-medium text-brand @endif">
                                            <x-icon :class="$job->icon_class" />
                                            {{ $job->name }}
                                        </h3>
                                        <a
                                            href="{{ $job->website }}"
                                            target="_blank"
                                            class="inline-block p-1 text-xs text-snow-20 hover:text-brand dark:text-snow-10"
                                        >
                                            {{ $job->website_host }}
                                        </a>
                                    </div>
                                    <aside class="text-right text-sm">
                                        <div>
                                            <time datetime="{{ $job->start_at->toIso8601String() }}"> {{ $job->start_at->year }} </time>
                                            -
                                            <time datetime="{{ ($job->end_at ?? now())->toIso8601String() }}"> {{ optional($job->end_at)->year ?? 'now' }} </time>
                                        </div>
                                        <span class="text-snow-20 dark:text-snow-10">{{ \Illuminate\Support\Str::money($job->salary ?? 0) }}</span>
                                    </aside>
                                </div>
                                <strong class="block @if(!$job->has_end) font-bold @else text-sm font-normal @endif">{{ $job->role }}</strong>
                                <ul class="flex list-none space-x-4 @if(!$job->has_end) text-sm @else text-xs @endif mt-1">
                                    @foreach ($job->stack as $tool)
                                        <li>{{ $tool }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-section>

    <x-section>
        <div class="mx-auto prose mb-8 w-full sm:max-w-screen-sm sm:px-4 md:prose-lg md:max-w-screen-md md:px-0 lg:prose-xl">
            <h2>Hacktoberfest</h2>
            <p>A monthlong celebration of open source software hosted by DigitalOcean, Intel and DEV. Hacktoberfest is open to everyone in the global community. All backgrounds and skill levels are encouraged to complete the challenge.</p>
            <small class="block"
                ><a
                    href="https://hacktoberfest.digitalocean.com"
                    target="_blank"
                >
                    hacktoberfest.digitalocean.com
                </a></small
            >
        </div>
        <x-grid class="xl:grid-cols-4">
            @foreach ($hacktoberfests as $hacktoberfest)
                <div class="overflow-hidden rounded-4 bg-white shadow dark:bg-night-20">
                    <x-img
                        :src="$hacktoberfest->image"
                        width="1024"
                        height="512"
                        :alt="$hacktoberfest->name"
                    />
                    <div class="px-4 py-2">
                        <strong class="block">{{ $hacktoberfest->name }}</strong>
                        <p class="text-sm text-snow-20 dark:text-snow-10">{{ $hacktoberfest->description }}</p>
                    </div>
                </div>
            @endforeach
        </x-grid>
    </x-section>
@endsection
