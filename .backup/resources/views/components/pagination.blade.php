<?php /** @var App\Services\Paginator $paginator */ ?>

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-between items-center">
        <div>
            @if (!$paginator->onFirstPage())
                <a
                    href="{{ $paginator->previousPageUrl() }}"
                    rel="prev"
                    class="inline-block w-10 h-10 leading-10 text-center bg-white rounded-full shadow dark:bg-night-20 hover:bg-brand hover:text-white"
                >
                    <x-icon class="fal fa-chevron-left"/>
                </a>
            @else
                <span class="inline-block w-10 h-10"></span>
            @endif
        </div>
        <div>
            <ul class="flex space-x-4 list-none">
                @if($paginator->currentPage() !== 1)
                    <li>
                        <a
                            href="{{ $paginator->url(1) }}"
                            class="inline-block w-10 h-10 leading-10 text-center bg-white rounded-full shadow dark:bg-night-20 hover:bg-brand hover:text-white"
                        >1</a>
                    </li>
                @endif
                <li>
                    <span class="inline-block w-10 h-10 leading-10 text-center text-white rounded-full shadow bg-brand">
                        {{ $paginator->currentPage() }}
                    </span>
                </li>
                @if($paginator->currentPage() !== $paginator->lastPage())
                    <li>
                        <a
                            href="{{ $paginator->url($paginator->lastPage()) }}"
                            class="inline-block w-10 h-10 leading-10 text-center bg-white rounded-full shadow dark:bg-night-20 hover:bg-brand hover:text-white"
                        >{{ $paginator->lastPage() }}</a>
                    </li>
                @endif
            </ul>
        </div>
        <div>
            @if ($paginator->hasMorePages())
                <a
                    href="{{ $paginator->nextPageUrl() }}"
                    rel="next"
                    class="inline-block w-10 h-10 leading-10 text-center bg-white rounded-full shadow dark:bg-night-20 hover:bg-brand hover:text-white"
                >
                    <x-icon class="fal fa-chevron-right"/>
                </a>
            @else
                <span class="inline-block w-10 h-10"></span>
            @endif
        </div>
    </nav>
@endif