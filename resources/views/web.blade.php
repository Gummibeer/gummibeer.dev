<?php /** @var App\Services\MetaBag $meta */ ?>

<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="antialiased"
>
<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    />

    @if (app()->environment('prod'))
        <link
            rel="dns-prefetch"
            href="https://static.cloudflareinsights.com"
        />
        <link
            rel="dns-prefetch"
            href="https://cloudflareinsights.com"
        />
    @endif

    <title>{{ $meta->title }}</title>
    @if ($meta->description)
        <meta
            name="description"
            content="{{ $meta->description }}"
        />
    @endif

    <meta
        name="theme-color"
        content="#ffb300"
    />
    <meta
        name="msapplication-TileColor"
        content="#ffb300"
    />

    <x-favicons />

    @vite (['resources/css/app.css', 'resources/js/app.js'])

    <link
        rel="me"
        href="https://twitter.com/devgummibeer"
    />
    <link
        rel="me"
        href="https://github.com/Gummibeer"
    />
    <link
        rel="me"
        href="https://instagram.com/dev.gummibeer"
    />

    <x-webmention-links />
    <link
        rel="sitemap"
        type="application/xml"
        href="{{ route('sitemap.xml') }}"
    />
    <link
        rel="canonical"
        href="{{ request()->url() }}"
    />
    @stack ('head')
</head>
<body class="line-numbers min-h-screen bg-snow-0 text-night-0 dark:bg-night-0 dark:text-snow-0">
    <x-menu />

    <div class="relative">
        @yield ('content')
    </div>

    <x-footer />

    @if (app()->environment('prod'))
        <script
            async
            defer
            data-website-id="5790432b-8e52-4f5d-b458-937bb1ddedc6"
            src="https://u.gummibeer.dev/script.js"
        ></script>
    @endif
</body>
</html>
