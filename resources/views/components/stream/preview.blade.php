<?php /** @var Illuminate\View\ComponentAttributeBag $attributes */ ?>
<?php /** @var Illuminate\Support\HtmlString $slot */ ?>
<?php /** @var Statamic\Contracts\Entries\Entry $stream */ ?>
@props (['stream'])

<article class="overflow-hidden rounded-4 bg-white shadow dark:bg-night-20">
    <a href="{{ $stream->video }}">
        <x-img
            :src="$stream->image"
            width="768"
            ratio="16:9"
            :crop="true"
        />
    </a>
    <div class="p-4">
        <div class="mb-4 text-brand">
            <x-icon class="fab fa-youtube mr-1" />
            <strong class="uppercase"> stream </strong>
        </div>
        <h3 class="mb-4 text-2xl leading-none font-bold text-night-0 dark:text-white">
            <a
                href="{{ $stream->video }}"
                class="hover:underlined"
            >
                {{ $stream->title }}
            </a>
        </h3>
        <x-stream.aside
            :stream="$stream"
            class="text-sm"
        />
    </div>
</article>
