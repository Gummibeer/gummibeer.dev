<?php /** @var Illuminate\View\ComponentAttributeBag $attributes */ ?>
<?php /** @var Illuminate\Support\HtmlString $slot */ ?>
<?php /** @var string $name */ ?>
<?php /** @var string $lang */ ?>

<div class="mt-4 mb-6">
    <header class="flex space-x-2 rounded-t-2 bg-snow-10 px-4 dark:bg-night-20">
        <div class="py-2 text-right text-xs leading-none font-bold uppercase">{{ $lang }}</div>
        <div class="grow truncate py-2 text-center font-mono text-xs leading-none">{{ $name }}</div>
        <button
            class="py-2 text-xs leading-none"
            type="button"
            data-clipboard-text="{{ $slot }}"
            title="copy code to clipboard"
        >
            <x-icon name="ski-copy" />
            <span class="sr-only">copy code to clipboard</span>
        </button>
    </header>
    <section class="rounded-b-2 border-2 border-t-0 border-snow-10 bg-white dark:border-night-20 dark:bg-night-10">
        <pre><code class="language-{{ $lang }}">{{ $slot }}</code></pre>
    </section>
</div>
