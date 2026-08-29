<?php /** @var App\Post $post */ ?>
<?php /** @var App\Services\MetaBag $meta */ ?>

@extends('web')

@push('head')
    <link rel="index" href="{{ route('blog.index') }}">
    <x-og.article :post="$post"/>
    @if($post->author->payment_pointer)
        <meta name="monetization" content='{{ $post->author->payment_pointer }}'>
    @endif
@endpush

@section('content')
    <x-article class="markdown">
        <header class="mb-8">
            <x-post.image :post="$post"/>
            @if($post->categories()->isNotEmpty())
                <x-post.ul-categories :post="$post" class="mb-4"/>
            @endif
            <x-post.aside :post="$post"/>
        </header>
        <main class="prose md:prose-lg lg:prose-xl">
            <h1>{{ $post->title }}</h1>
            {{ $post->contents }}
        </main>
        <script src="https://utteranc.es/client.js"
            repo="Gummibeer/gummibeer.de"
            issue-term="pathname"
            label="💬 comment"
            theme="github-light"
            crossorigin="anonymous"
            async
        ></script>
        <x-post.webmentions :url="$post->url" class="pt-12 mt-12 border-t-2 border-snow-10"/>
    </x-article>
@endsection