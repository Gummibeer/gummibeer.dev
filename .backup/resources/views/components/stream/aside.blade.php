<?php /** @var Illuminate\View\ComponentAttributeBag $attributes */ ?>
<?php /** @var Illuminate\Support\HtmlString $slot */ ?>
<?php /** @var \Spatie\Sheets\Sheet $stream */ ?>
@props(['stream'])

<aside {{ $attributes->merge(['class' => 'text-snow-20 dark:text-snow-10']) }}>
    <ul class="flex flex-col list-none sm:flex-row sm:space-x-3">
        <li>
            <x-icon class="mr-1 fal fa-calendar"/>
            <time datetime="{{ $stream->date->format('Y-m-d') }}">
                {{ $stream->date->format('M jS, Y') }}
            </time>
        </li>
        <li>
            <x-icon class="mr-1 fal fa-clock"/>
            {{ $stream->duration->forHumans(['minimumUnit' => 'minute']) }}
        </li>
    </ul>
</aside>