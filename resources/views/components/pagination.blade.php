<?php /** @var App\Services\Paginator $paginator */ ?>

@if ($paginator->hasPages())
    <nav
        role="navigation"
        aria-label="Pagination Navigation"
        class="flex items-center justify-between"
    >
        <div>
            @if (!$paginator->onFirstPage())
                <a
                    href="{{ $paginator->previousPageUrl() }}"
                    rel="prev"
                    class="inline-block h-10 w-10 rounded-full bg-white text-center leading-10 shadow hover:bg-brand hover:text-white dark:bg-night-20"
                >
                    <x-icon class="fa-solid fa-chevron-left" />
                </a>
            @else
                <span class="inline-block h-10 w-10"></span>
            @endif
        </div>
        <div>
            <ul class="flex list-none space-x-4">
                @if ($paginator->currentPage() !== 1)
                    <li>
                        <a
                            href="{{ $paginator->url(1) }}"
                            class="inline-block h-10 w-10 rounded-full bg-white text-center leading-10 shadow hover:bg-brand hover:text-white dark:bg-night-20"
                            >1</a
                        >
                    </li>
                @endif
                <li>
                    <span class="inline-block h-10 w-10 rounded-full bg-brand text-center leading-10 text-white shadow"> {{ $paginator->currentPage() }} </span>
                </li>
                @if ($paginator->currentPage() !== $paginator->lastPage())
                    <li>
                        <a
                            href="{{ $paginator->url($paginator->lastPage()) }}"
                            class="inline-block h-10 w-10 rounded-full bg-white text-center leading-10 shadow hover:bg-brand hover:text-white dark:bg-night-20"
                            >{{ $paginator->lastPage() }}</a
                        >
                    </li>
                @endif
            </ul>
        </div>
        <div>
            @if ($paginator->hasMorePages())
                <a
                    href="{{ $paginator->nextPageUrl() }}"
                    rel="next"
                    class="inline-block h-10 w-10 rounded-full bg-white text-center leading-10 shadow hover:bg-brand hover:text-white dark:bg-night-20"
                >
                    <x-icon class="fa-solid fa-chevron-right" />
                </a>
            @else
                <span class="inline-block h-10 w-10"></span>
            @endif
        </div>
    </nav>
@endif
