<?php /** @var string $id */ ?>
<?php /** @var string $path */ ?>
<?php /** @var string $prompt */ ?>
<?php /** @var string $title */ ?>

{{-- prettier-ignore --}}
<div class="not-prose relative my-6 overflow-hidden rounded-2 border-2 border-snow-10 bg-white dark:border-night-20 dark:bg-night-10">
    <details>
        <summary class="cursor-pointer bg-snow-10 px-4 py-3 pr-24 font-bold dark:bg-night-20">{{ $title }}</summary>
        <pre
            id="{{ $id }}"
            class="m-0 overflow-x-auto p-4 font-mono text-sm leading-snug break-words whitespace-pre-wrap text-inherit"
        >{{ $prompt }}</pre>
    </details>
    <div class="absolute top-0 right-0 flex h-12 items-center gap-2 px-4">
        <a
            class="p-1 hover:text-brand"
            href="{{ $path }}"
            download
            title="download prompt"
        >
            <x-icon name="ski-download" />
            <span class="sr-only">download prompt</span>
        </a>
        <button
            class="p-1 hover:text-brand"
            type="button"
            data-clipboard-target="#{{ $id }}"
            title="copy prompt to clipboard"
        >
            <x-icon name="ski-copy" />
            <span class="sr-only">copy prompt to clipboard</span>
        </button>
    </div>
</div>
