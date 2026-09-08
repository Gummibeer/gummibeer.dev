<?php /** @var Statamic\Contracts\Taxonomies\Term $category */ ?>
@props (['category'])

@php
    $entries = \Statamic\Facades\Entry::query()
        ->whereIn('collection', ['posts', 'streams'])
        ->whereStatus('published')
        ->whereTaxonomy($category->id())
        ->get()
        ->sortByDesc(fn ($entry) => $entry->date()?->timestamp ?? 0);
@endphp

<x-section>
    <h1 class="mb-8 text-6xl leading-none font-black text-night-0">{{ str($category->title())->headline() }}</h1>
    <div class="mb-8 grid grid-flow-row grid-cols-1 gap-4 md:grid-cols-2 md:gap-8 lg:grid-cols-3 lg:gap-10 xl:gap-12">
        @foreach ($entries as $entry)
            @if ($entry->collection()->handle() === 'posts')
                <x-post.preview :post="$entry" />
            @elseif ($entry->collection()->handle() === 'streams')
                <x-stream.preview :stream="$entry" />
            @endif
        @endforeach
    </div>
</x-section>
