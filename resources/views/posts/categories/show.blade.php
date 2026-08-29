<?php /** @var Statamic\Taxonomies\LocalizedTerm $page */ ?>

@extends ('web')

@push ('head')
    <x-og.website />
    <x-link-feed
        route="blog.category.feed"
        :parameters="['category' => $page->slug()]"
    />
@endpush

@section ('content')
    @php($posts = $entries->get())

    <x-section>
        <h1 class="mb-8 text-6xl leading-none font-black text-night-0 dark:text-white">Posts about {{ $page->title }}</h1>
        <div class="mb-8 grid grid-flow-row grid-cols-1 gap-4 md:grid-cols-2 md:gap-8 lg:grid-cols-3 lg:gap-10 xl:gap-12">
            @foreach ($posts as $post)
                <x-post.preview :post="$post" />
            @endforeach
        </div>
    </x-section>
@endsection
