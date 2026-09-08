@props([
    'id',
    'title',
    'content',
    'path' => null,
    'copyLabel' => 'copy text to clipboard',
    'downloadLabel' => 'download text',
])

{{-- prettier-ignore --}}
<div class="not-prose relative my-6 overflow-hidden rounded-2 border-2 border-snow-10 bg-white dark:border-night-20 dark:bg-night-10">
    <details>
        <summary class="cursor-pointer bg-snow-10 px-4 py-3 pr-24 font-bold dark:bg-night-20">{{ $title }}</summary>
        <pre
            id="{{ $id }}"
            class="m-0 overflow-x-auto p-4 font-mono text-sm leading-snug break-words whitespace-pre-wrap text-inherit"
        >{{ $content }}</pre>
    </details>
    <div class="absolute top-0 right-0 flex h-12 items-center gap-2 px-4">
        @if ($path)
            <a
                class="p-1 hover:text-brand"
                href="{{ $path }}"
                download
                title="{{ $downloadLabel }}"
            >
                <x-icon name="ski-download" />
                <span class="sr-only">{{ $downloadLabel }}</span>
            </a>
        @endif
        <button
            class="p-1 hover:text-brand"
            type="button"
            data-clipboard-target="#{{ $id }}"
            title="{{ $copyLabel }}"
        >
            <x-icon name="ski-copy" />
            <span class="sr-only">{{ $copyLabel }}</span>
        </button>
    </div>
</div>
