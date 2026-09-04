@cascade ([
    'page' => null,
])

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

    @if (app()->environment('production'))
        <link
            rel="dns-prefetch"
            href="https://static.cloudflareinsights.com"
        />
        <link
            rel="dns-prefetch"
            href="https://cloudflareinsights.com"
        />
    @endif

    <title>{{ $page?->title ? $page->title.' | '.($identity?->site_name ?? config('app.name')) : ($identity?->site_name ?? config('app.name')) }}</title>
    @if ($page?->description)
        <meta
            name="description"
            content="{{ $page->description }}"
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

    @if ($identity)
        <link
            rel="me"
            href="{{ $identity->github_url }}"
        />
    @endif

    <link
        rel="sitemap"
        type="application/xml"
        href="{{ route('sitemap.xml') }}"
    />
    <link
        rel="alternate"
        type="text/plain"
        href="{{ url('/llms.txt') }}"
        title="LLMs"
    />
    <link
        rel="canonical"
        href="{{ $page?->permalink ?? request()->url() }}"
    />
    @if ($page?->permalink)
        <link
            rel="alternate"
            type="text/markdown"
            href="{{ request()->path() === '/' ? url('/index.md') : url('/'.request()->path().'.md') }}"
            title="Markdown"
        />
    @endif
    @stack ('head')
</head>
<body class="line-numbers flex min-h-dvh flex-col bg-snow-0 text-night-0 dark:bg-night-0 dark:text-snow-0">
    <x-menu :identity="$identity" />

    <main class="relative flex-1">
        @yield ('content')
    </main>

    <x-footer :identity="$identity" />

    @if (app()->environment('production'))
        <script
            async
            defer
            data-website-id="5790432b-8e52-4f5d-b458-937bb1ddedc6"
            src="https://u.gummibeer.dev/script.js"
        ></script>
    @endif
</body>
</html>
