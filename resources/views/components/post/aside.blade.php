<?php /** @var Illuminate\View\ComponentAttributeBag $attributes */ ?>
<?php /** @var Illuminate\Support\HtmlString $slot */ ?>
<?php /** @var Statamic\Contracts\Entries\Entry $post */ ?>

<aside {{ $attributes->except('post')->merge(['class' => 'text-snow-20 dark:text-snow-10']) }}>
    <ul class="flex list-none flex-col sm:flex-row sm:space-x-3">
        <li>
            <x-icon class="fal fa-calendar mr-1" />
            <time datetime="{{ $post->date->format('Y-m-d') }}"> {{ $post->date->format('M jS, Y') }} </time>
        </li>
        <li>
            <x-icon class="fal fa-clock mr-1" />
            {{ $post->read_time }} min read
        </li>
    </ul>
</aside>
