<?php /** @var App\Services\Paginator $posts */ ?>

@extends('web')

@push('head')
    <x-og.website/>
    <x-link-pagination :paginator="$posts"/>
    <x-link-feed route="blog.feed"/>
@endpush

@section('content')
    <x-section>
        <h1 class="mb-8 text-6xl font-black leading-none text-night-0 dark:text-white">Blog</h1>
        <x-post.search/>
        <div class="grid grid-cols-1 grid-flow-row gap-4 mb-8 md:grid-cols-2 lg:grid-cols-3 md:gap-8 lg:gap-10 xl:gap-12">
            @foreach($posts as $entry)
                @if($entry->collection()->handle() === 'posts')
                    <x-post.preview :post="$entry"/>
                @elseif($entry->collection()->handle() === 'streams')
                    <x-stream.preview :stream="$entry"/>
                @endif
            @endforeach
        </div>
        <x-pagination :paginator="$posts"></x-pagination>
    </x-section>
@endsection