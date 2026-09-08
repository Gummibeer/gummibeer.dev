<?php /** @var Illuminate\View\ComponentAttributeBag $attributes */ ?>
<?php /** @var Illuminate\Support\HtmlString $slot */ ?>
<?php /** @var Statamic\Contracts\Entries\Entry $stream */ ?>
@props (['stream'])

<article class="overflow-hidden rounded-4 bg-white shadow">
    <a href="{{ $stream->url() }}">
        <x-img
            :src="$stream->image"
            width="768"
            ratio="16:9"
            :crop="true"
        />
    </a>
    <div class="p-4">
        <div class="mb-4 flex items-center gap-3 text-brand">
            <span>
                <x-icon
                    name="fab-youtube"
                    class="mr-1"
                />
                <strong class="uppercase"> stream </strong>
            </span>
            @if ($stream->category)
                <x-post.category :category="$stream->category" />
            @endif
        </div>
        <h3 class="mb-4 text-2xl leading-none font-bold text-night-0">
            <a
                href="{{ $stream->url() }}"
                class="hover:underlined"
            >
                {{ $stream->title }}
            </a>
        </h3>
        @if ($stream->description)
            <p class="mb-4 text-sm text-snow-20">{{ str($stream->description)->squish()->limit(180) }}</p>
        @endif
        <x-stream.aside
            :stream="$stream"
            class="text-sm"
        />
    </div>
</article>
