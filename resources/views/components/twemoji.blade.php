<?php /** @var Illuminate\View\ComponentAttributeBag $attributes */ ?>
<?php /** @var Illuminate\Support\HtmlString $slot */ ?>

<img
    src="{{ \Astrotomic\Twemoji\Twemoji::emoji($slot)->base('https://cdn.jsdelivr.net/gh/jdecked/twemoji@latest/assets')->svg()->url() }}"
    alt="Emoji {{ $slot }}"
    loading="lazy"
    {{ $attributes }}
/>
