@cascade ([
    'site',
    'page' => null,
])

<meta
    property="og:type"
    content="website"
/>
<meta
    property="og:title"
    content="{{ $page?->title ? $page->title.' | '.$site->site_name : $site->site_name }}"
/>
<meta
    property="og:url"
    content="{{ $page?->permalink ?? request()->url() }}"
/>
<meta
    property="og:locale"
    content="{{ str_replace('-', '_', app()->getLocale()) }}"
/>
@if ($page?->description)
    <meta
        property="og:description"
        content="{{ $page->description }}"
    />
@endif
<meta
    name="twitter:card"
    content="summary_large_image"
/>
<meta
    name="twitter:title"
    content="{{ $page?->title ? $page->title.' | '.$site->site_name : $site->site_name }}"
/>
@if ($page?->description)
    <meta
        name="twitter:description"
        content="{{ $page->description }}"
    />
@endif
