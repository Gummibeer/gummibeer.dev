<?php /** @var Illuminate\Support\HtmlString $contents */ ?>
<?php /** @var array[] $projects */ ?>

@extends('web')

@push('head')
    <x-og.website/>
@endpush

@section('content')
    <x-article class="prose md:prose-lg lg:prose-xl">
        {{ $contents }}
    </x-article>

    <x-section class="bg-dotted">
        <x-grid class="xl:grid-cols-4">
            @foreach(collect($projects)->sortBy('name') as $project)
                <div class="overflow-hidden bg-white shadow rounded-4 dark:bg-night-20">
                    @isset($project['src'])
                    <a href="{{ $project['href'] }}" target="_blank" rel="noreferrer noopener" class="block pb-1">
                        <x-img
                            :src="$project['src']"
                            width="768"
                            ratio="16:9"
                            :alt="$project['name']"
                        />
                    </a>
                    @endisset
                    <div class="py-2 px-4">
                        <a href="{{ $project['href'] }}" target="_blank" rel="noreferrer noopener" class="block hover:text-brand">
                            <strong>{{ $project['name'] }}</strong>
                        </a>
                        <p class="text-sm text-snow-20 dark:text-snow-10">{{ $project['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </x-grid>
    </x-section>
@endsection