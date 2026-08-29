<?php /** @var Illuminate\View\ComponentAttributeBag $attributes */ ?>
<?php /** @var Illuminate\Support\HtmlString $slot */ ?>

<section {{ $attributes->merge(['class' => 'py-8 px-4 md:px-8 lg:px-10 xl:px-12']) }}>
    {{ $slot }}
</section>