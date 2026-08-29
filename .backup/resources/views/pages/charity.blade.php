<?php /** @var Illuminate\Support\HtmlString $contents */ ?>
<?php /** @var array[] $charities */ ?>

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
            @foreach(collect($charities)->sortBy('name') as $charity)
                <div class="overflow-hidden bg-white shadow rounded-4 dark:bg-night-20">
                    <a href="{{ $charity['href'] }}" target="_blank" rel="noreferrer noopener" class="block pb-1">
                        <x-figure>
                            <x-img
                                :src="$charity['src']"
                                width="768"
                                ratio="16:9"
                                :alt="$charity['name']"
                            />
                            <x-slot name="caption">{{ $charity['name'] }}</x-slot>
                        </x-figure>
                    </a>
                </div>
            @endforeach
        </x-grid>
    </x-section>
@endsection