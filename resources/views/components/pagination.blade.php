@if ($paginate['total_pages'] > 1)
    <nav
        role="navigation"
        aria-label="Pagination Navigation"
        class="flex items-center justify-between"
    >
        <div>
            @if ($paginate['prev_page'])
                <a
                    href="{{ $paginate['prev_page'] }}"
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
                @if ($paginate['current_page'] !== 1)
                    <li>
                        <a
                            href="{{ $paginate['links']['all'][0]['url'] }}"
                            class="inline-block h-10 w-10 rounded-full bg-white text-center leading-10 shadow hover:bg-brand hover:text-white dark:bg-night-20"
                        >1</a>
                    </li>
                @endif
                <li>
                    <span class="inline-block h-10 w-10 rounded-full bg-brand text-center leading-10 text-white shadow">{{ $paginate['current_page'] }}</span>
                </li>
                @if ($paginate['current_page'] !== $paginate['total_pages'])
                    <li>
                        <a
                            href="{{ $paginate['links']['all'][$paginate['total_pages'] - 1]['url'] }}"
                            class="inline-block h-10 w-10 rounded-full bg-white text-center leading-10 shadow hover:bg-brand hover:text-white dark:bg-night-20"
                        >{{ $paginate['total_pages'] }}</a>
                    </li>
                @endif
            </ul>
        </div>
        <div>
            @if ($paginate['next_page'])
                <a
                    href="{{ $paginate['next_page'] }}"
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
