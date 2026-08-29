<?php /** @var App\Services\MetaBag $meta */ ?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="dns-prefetch" href="{{ config('app.asset_url') }}" id="ASSET_URL">
    @if(config('services.imgix.domain'))
        <link rel="dns-prefetch" href="https://{{ config('services.imgix.domain') }}">
    @endif
    @if(app()->environment('prod'))
        <link rel="dns-prefetch" href="https://static.cloudflareinsights.com">
        <link rel="dns-prefetch" href="https://cloudflareinsights.com">
        <link rel="dns-prefetch" href="https://search.gummibeer.dev" id="SEARCH_URL">
    @endif

    <title>{{ $meta->title }}</title>
    @if($meta->description)
        <meta name="description" content="{{ $meta->description }}">
    @endif

    <meta name="theme-color" content="#ffb300">
    <meta name="msapplication-TileColor" content="#ffb300">

    <x-favicons/>

    <link href="{{ mix('css/app.css') }}" rel="stylesheet" crossorigin="anonymous">

    <link rel="me" href="https://twitter.com/devgummibeer">
    <link rel="me" href="https://github.com/Gummibeer">
    <link rel="me" href="https://instagram.com/dev.gummibeer">

    <x-webmention-links />
    <link rel="sitemap" type="application/xml" href="{{ route('sitemap.xml') }}">
    <link rel="canonical" href="{{ request()->url() }}">
    @stack('head')
</head>
<body class="min-h-screen bg-snow-0 text-night-0 dark:bg-night-0 dark:text-snow-0 line-numbers">
<x-menu/>

<div class="relative">
    @yield('content')
</div>

<x-footer/>

<script defer src="{{ mix('js/app.js') }}" crossorigin="anonymous"></script>
@if(app()->environment('prod'))
    <script async defer data-website-id="5790432b-8e52-4f5d-b458-937bb1ddedc6" src="https://u.gummibeer.dev/script.js"></script>
@endif
</body>
</html>
