<?php /** @var Statamic\Contracts\Entries\Entry $page */ ?>

@extends ('web')

@push ('head')
    <link
        rel="index"
        href="{{ $page->collection()->url() }}"
    />
    <x-og.article
        :post="$page"
        :site-name="$identity->site_name"
    />
@endpush

@section ('content')
    <x-article class="markdown">
        <header class="mb-8">
            <x-post.image :post="$page" />
            @if ($page->categories->isNotEmpty())
                <x-post.ul-categories
                    :post="$page"
                    class="mb-4"
                />
            @endif
            <x-post.aside :post="$page" />
        </header>
        <main class="prose md:prose-lg lg:prose-xl">
            <h1>{{ $page->title }}</h1>
            {!! $page->content !!}
        </main>
        <x-comments :post="$page" />
    </x-article>
@endsection
