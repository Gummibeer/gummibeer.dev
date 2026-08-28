<?php /** @var Illuminate\View\ComponentAttributeBag $attributes */ ?>
<?php /** @var Illuminate\Support\HtmlString $slot */ ?>
<?php /** @var Illuminate\Support\HtmlString $caption */ ?>

<figure
    @if (!empty((string) $caption)) role="group" @endif
    {{ $attributes->merge(['class' => 'overflow-hidden']) }}
>
    {{ $slot }}
    @if (!empty((string) $caption))
        <figcaption class="mt-1 text-center text-sm text-snow-20 dark:text-snow-10">{!! \Statamic\Facades\Markdown::parse((string) $caption) !!}</figcaption>
    @endif
</figure>
