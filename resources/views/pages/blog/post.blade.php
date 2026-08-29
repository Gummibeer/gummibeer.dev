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
        :twitter-handle="$identity->twitter_handle"
    />
    @if ($page->author?->payment_pointer)
        <meta
            name="monetization"
            content='{{ $page->author->payment_pointer }}'
        />
    @endif
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
        <script
            src="https://utteranc.es/client.js"
            repo="Gummibeer/gummibeer.de"
            issue-term="pathname"
            label="💬 comment"
            theme="github-light"
            crossorigin="anonymous"
            async
        ></script>
        <x-post.webmentions
            :url="$page->permalink"
            class="mt-12 border-t-2 border-snow-10 pt-12"
        />
    </x-article>
@endsection
