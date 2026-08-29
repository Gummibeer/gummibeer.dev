<?php /** @var Illuminate\View\ComponentAttributeBag $attributes */ ?>
<?php /** @var Illuminate\Support\HtmlString $slot */ ?>
<?php /** @var string $name */ ?>
<?php /** @var string $lang */ ?>

<div class="mt-4 mb-6">
    <header class="flex px-4 space-x-2 bg-snow-10 dark:bg-night-20 rounded-t-2">
        <div class="py-2 text-xs font-bold leading-none text-right uppercase">
            {{ $lang }}
        </div>
        <div class="flex-grow py-2 font-mono text-xs leading-none text-center truncate">
            {{ $name }}
        </div>
        <button class="py-2 text-xs leading-none" type="button" data-clipboard-text="{{ $slot }}" title="copy code to clipboard">
            <x-icon class="fal fa-copy"/>
            <span class="sr-only">copy code to clipboard</span>
        </button>
    </header>
    <section class="bg-white border-2 border-t-0 dark:bg-night-10 border-snow-10 dark:border-night-20 rounded-b-2">
        <pre><code class="language-{{ $lang }}">{{ $slot }}</code></pre>
    </section>
</div>