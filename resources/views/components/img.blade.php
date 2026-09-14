<picture>
    <source
        type="image/webp"
        srcset="{{ $srcSet('webp') }}"
    />
    <img
        src="{{ $src() }}"
        srcset="{{ $srcSet() }}"
        @if ($width) width="{{ $width }}" @endif
        @if ($height) height="{{ $height }}" @endif
        loading="lazy"
        {{ $attributes->merge(['class' => 'w-full h-auto']) }}
    />
</picture>
