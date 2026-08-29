<?php /** @var Illuminate\View\ComponentAttributeBag $attributes */ ?>
<?php /** @var Illuminate\Support\HtmlString $slot */ ?>

<article {{ $attributes->merge(['class' => 'py-8 px-4 md:px-0 mx-auto w-full sm:max-w-screen-sm md:max-w-screen-md']) }}>
    {{ $slot }}
</article>