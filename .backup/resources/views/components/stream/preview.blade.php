<?php /** @var Illuminate\View\ComponentAttributeBag $attributes */ ?>
<?php /** @var Illuminate\Support\HtmlString $slot */ ?>
<?php /** @var \Spatie\Sheets\Sheet $stream */ ?>
@props(['stream'])

<article class="rounded-4 shadow bg-white dark:bg-night-20 overflow-hidden">
    <a href="https://youtu.be/{{ $stream->youtube_id }}">
        <x-img
            src="https://i.ytimg.com/vi/{{ $stream->youtube_id }}/maxresdefault.jpg"
            width="768"
            ratio="16:9"
            :crop="true"
        />
    </a>
    <div class="p-4">
        <div class="mb-4 text-brand">
            <x-icon class="mr-1 fab fa-youtube"/>
            <strong class="uppercase">
                stream
            </strong>
        </div>
        <h3 class="mb-4 text-2xl font-bold leading-none text-night-0 dark:text-white">
            <a href="https://youtu.be/{{ $stream->youtube_id }}" class="hover:underlined">
                {{ $stream->title }}
            </a>
        </h3>
        <x-stream.aside :stream="$stream" class="text-sm"/>
    </div>
</article>