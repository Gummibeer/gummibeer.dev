<?php /** @var Illuminate\View\ComponentAttributeBag $attributes */ ?>
<?php /** @var Illuminate\Support\HtmlString $slot */ ?>

<img
    src="{{ \Astrotomic\Twemoji\Twemoji::emoji($slot)->base(asset('vendor/twemoji'))->svg()->url() }}"
    alt="Emoji {{ $slot }}"
    loading="lazy"
    {{ $attributes }}
/>