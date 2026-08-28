{{-- https://favicon.io/emoji-favicons/panda --}}
@php
    $glide = app(\Statamic\Contracts\Imaging\UrlBuilder::class);
    $favicon = static fn (string $path): string => $glide->build('images::favicons/'.$path, []);
@endphp
<link
    rel="apple-touch-icon"
    sizes="180x180"
    href="{{ $favicon('apple-touch-icon.png') }}"
/>
<link
    rel="icon"
    type="image/png"
    sizes="512x512"
    href="{{ $favicon('favicon-512x512.png') }}"
/>
<link
    rel="icon"
    type="image/png"
    sizes="192x192"
    href="{{ $favicon('favicon-192x192.png') }}"
/>
<link
    rel="icon"
    type="image/png"
    sizes="32x32"
    href="{{ $favicon('favicon-32x32.png') }}"
/>
<link
    rel="icon"
    type="image/png"
    sizes="16x16"
    href="{{ $favicon('favicon-16x16.png') }}"
/>
