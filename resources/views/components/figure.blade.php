<figure
    @unless (empty((string) $caption)) role="group" @endunless
    {{ $attributes->merge(['class' => 'overflow-hidden']) }}
>
    {{ $slot }}
    @unless (empty((string) $caption))
        <figcaption class="mt-1 text-center text-sm text-snow-20">{!! \Statamic\Facades\Markdown::parse((string) $caption) !!}</figcaption>
    @endunless
</figure>
