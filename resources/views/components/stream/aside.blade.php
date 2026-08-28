<?php /** @var Illuminate\View\ComponentAttributeBag $attributes */ ?>
<?php /** @var Illuminate\Support\HtmlString $slot */ ?>
<?php /** @var Statamic\Contracts\Entries\Entry $stream */ ?>
@props (['stream'])

<aside {{ $attributes->merge(['class' => 'text-snow-20 dark:text-snow-10']) }}>
    <ul class="flex list-none flex-col sm:flex-row sm:space-x-3">
        <li>
            <x-icon class="fal fa-calendar mr-1" />
            <time datetime="{{ $stream->date->format('Y-m-d') }}"> {{ $stream->date->format('M jS, Y') }} </time>
        </li>
        <li>
            <x-icon class="fal fa-clock mr-1" />
            {{ $stream->duration->forHumans(['minimumUnit' => 'minute']) }}
        </li>
    </ul>
</aside>
