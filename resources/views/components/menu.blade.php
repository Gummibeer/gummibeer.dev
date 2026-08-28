<?php /** @var App\Services\SiteIdentity $identity */ ?>

<header class="sticky top-0 right-0 left-0 z-10 bg-white shadow dark:bg-night-10">
    <nav
        class="flex flex-col px-4 md:flex-row md:justify-between md:px-8 lg:px-10 xl:px-12"
        x-data="{ show: false }"
        :class="{ 'h-screen': show }"
    >
        <div class="flex w-full flex-row items-center">
            <div class="flex grow items-center md:flex-auto">
                <a
                    href="{{ url('/') }}"
                    class="inline-block px-0 py-4 font-logo text-2xl leading-none tracking-wider whitespace-nowrap lg:text-3xl"
                >
                    {{ $identity->brandName() }}
                </a>
            </div>

            <button
                type="button"
                class="relative grid size-12 shrink-0 place-items-center md:hidden"
                @click="show = !show"
                aria-controls="menu-list"
                :aria-expanded="show ? 'true' : 'false'"
                aria-label="Toggle main menu"
            >
                <span
                    class="absolute h-0.5 w-6 bg-current transition-transform duration-200"
                    :class="show ? 'rotate-45' : '-translate-y-2'"
                ></span>
                <span
                    class="absolute h-0.5 w-6 bg-current transition-opacity duration-200"
                    :class="show ? 'opacity-0' : 'opacity-100'"
                ></span>
                <span
                    class="absolute h-0.5 w-6 bg-current transition-transform duration-200"
                    :class="show ? '-rotate-45' : 'translate-y-2'"
                ></span>
                <span class="sr-only">Toggle main menu</span>
            </button>
        </div>

        <ul
            class="flex w-full list-none flex-col md:w-auto md:flex-row md:space-x-2 lg:space-x-4"
            :class="{ 'hidden md:flex': !show }"
            x-cloak
            id="menu-list"
        >
            <statamic:nav:main max_depth="1">
                <li class="flex items-center">
                    <a
                        href="{{ $url }}"
                        class="@if ($is_current || ($url !== '/' && $is_parent)) text-brand @else text-black dark:text-white hover:text-brand @endif block w-full px-4 py-6 text-center text-2xl leading-none font-bold md:px-3 md:text-lg lg:px-4"
                        @if ($is_current) aria-current="page" @endif
                        >{{ $title }}</a
                    >
                </li>
            </statamic:nav:main>
        </ul>
    </nav>
</header>
