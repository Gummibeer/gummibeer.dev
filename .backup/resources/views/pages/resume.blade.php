<?php /** @var Illuminate\Support\HtmlString $contents */ ?>
<?php /** @var Illuminate\Support\Collection|App\Job[] $jobs */ ?>

@extends('web')

@push('head')
    <x-og.profile/>
@endpush

@section('content')
    <x-article class="prose md:prose-lg lg:prose-xl">
        {{ $contents }}
    </x-article>

    <x-section class="bg-dotted">
        <div class="mx-auto w-full sm:px-4 md:px-0 sm:max-w-screen-sm md:max-w-screen-md">
            <div class="overflow-hidden px-4 bg-white divide-y shadow rounded-4 dark:bg-night-20">
            @foreach($jobs as $job)
                <div class="py-4 @if($job->hasEnd()) text-snow-20 dark:text-snow-10 @endif">
                    <div class="flex flex-row sm:items-center sm:space-x-4">
                        @if($job->logo)
                            <div class="hidden w-24 h-24 sm:block">
                                <x-img
                                    :src="$job->logo"
                                    :alt="$job->name"
                                    class="object-contain w-full h-full"
                                />
                            </div>
                        @endif
                        <div class="flex-grow">
                            <div class="flex flex-col items-center justify-between sm:flex-row">
                                <div class="flex flex-col sm:flex-row sm:space-x-4 sm:items-center">
                                    <h3 class="text-2xl @if(!$job->hasEnd()) font-medium text-brand @endif">
                                        <x-icon :class="$job->icon"/>
                                        {{ $job->name }}
                                    </h3>
                                    <a
                                            href="{{ $job->website }}"
                                            target="_blank"
                                            class="inline-block p-1 text-xs text-snow-20 dark:text-snow-10 hover:text-brand"
                                    >
                                        {{ $job->website_host }}
                                    </a>
                                </div>
                                <aside class="text-sm text-right">
                                    <div>
                                        <time datetime="{{ $job->start_at->toIso8601String() }}">
                                            {{ $job->start_at->year }}
                                        </time>
                                        -
                                        <time datetime="{{ ($job->end_at ?? now())->toIso8601String() }}">
                                            {{ optional($job->end_at)->year ?? 'now' }}
                                        </time>
                                    </div>
                                    <span class="text-snow-20 dark:text-snow-10">{{ \Illuminate\Support\Str::money($job->salary ?? 0) }}</span>
                                </aside>
                            </div>
                            <strong class="block @if(!$job->hasEnd()) font-bold @else text-sm font-normal @endif">{{ $job->role }}</strong>
                            <ul class="flex list-none space-x-4 @if(!$job->hasEnd()) text-sm @else text-xs @endif mt-1">
                                @foreach($job->stack as $tool)
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
        <div class="mx-auto mb-8 w-full sm:px-4 md:px-0 sm:max-w-screen-sm md:max-w-screen-md prose md:prose-lg lg:prose-xl">
            <h2>Hacktoberfest</h2>
            <p>
                A monthlong celebration of open source software hosted by DigitalOcean, Intel and DEV.
                Hacktoberfest is open to everyone in the global community.
                All backgrounds and skill levels are encouraged to complete the challenge.
            </p>
            <small class="block"><a href="https://hacktoberfest.digitalocean.com" target="_blank">
                hacktoberfest.digitalocean.com
            </a></small>
        </div>
        <x-grid class="xl:grid-cols-4">
            @foreach($hacktoberfests as $hacktoberfest)
            <div class="overflow-hidden bg-white shadow rounded-4 dark:bg-night-20">
                <x-img
                    :src="$hacktoberfest->image"
                    width="1024"
                    height="512"
                    :alt="$hacktoberfest->name"
                />
                <div class="py-2 px-4">
                    <strong class="block">{{ $hacktoberfest->name }}</strong>
                    <p class="text-sm text-snow-20 dark:text-snow-10">{{ $hacktoberfest->description }}</p>
                </div>
            </div>
            @endforeach
        </x-grid>
    </x-section>
@endsection