<?php /** @var Illuminate\View\ComponentAttributeBag $attributes */ ?>
<?php /** @var Illuminate\Support\HtmlString $slot */ ?>
<?php /** @var Illuminate\Support\HtmlString $caption */ ?>
<?php /** @var League\CommonMark\CommonMarkConverter $markdown */ ?>

@inject('markdown', 'markdown')

<figure @if(!empty((string) $caption)) role="group" @endif {{ $attributes->merge(['class' => 'overflow-hidden']) }}>
    {{ $slot }}
    @if(!empty((string) $caption))
        <figcaption class="mt-1 text-sm text-center text-snow-20 dark:text-snow-10">
            {!! $markdown->convertToHtml($caption) !!}
        </figcaption>
    @endif
</figure>
