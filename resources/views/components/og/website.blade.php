@cascade ([
    'identity',
    'page' => null,
])

@php
    $image = $page?->og_image ?? null;
    $image = $image ? \App\Services\OpenGraphImage::url((string) $image) : null;
    $imageAlt = $page?->title ?? $identity->name;
@endphp

<meta
    property="og:type"
    content="website"
/>
<meta
    property="og:title"
    content="{{ $page?->title ? $page->title.' | '.$identity->name : $identity->name }}"
/>
<meta
    property="og:url"
    content="{{ $page?->permalink ?? request()->url() }}"
/>
<meta
    property="og:locale"
    content="{{ str_replace('-', '_', app()->getLocale()) }}"
/>
@if ($image)
    <meta
        property="og:image"
        content="{{ $image }}"
    />
    <meta
        property="og:image:type"
        content="image/png"
    />
    <meta
        property="og:image:width"
        content="{{ \App\Services\OpenGraphImage::WIDTH }}"
    />
    <meta
        property="og:image:height"
        content="{{ \App\Services\OpenGraphImage::HEIGHT }}"
    />
    <meta
        property="og:image:alt"
        content="{{ $imageAlt }}"
    />
@endif
@if ($page?->description)
    <meta
        property="og:description"
        content="{{ $page->description }}"
    />
@endif
<meta
    name="twitter:card"
    content="{{ $image ? 'summary_large_image' : 'summary' }}"
/>
<meta
    name="twitter:title"
    content="{{ $page?->title ? $page->title.' | '.$identity->name : $identity->name }}"
/>
@if ($image)
    <meta
        name="twitter:image"
        content="{{ $image }}"
    />
    <meta
        name="twitter:image:alt"
        content="{{ $imageAlt }}"
    />
@endif
@if ($page?->description)
    <meta
        name="twitter:description"
        content="{{ $page->description }}"
    />
@endif
