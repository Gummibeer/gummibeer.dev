<footer class="w-full bg-white px-4 py-4 text-snow-20 md:px-8 md:py-6 lg:px-10 xl:px-12 dark:bg-night-10 dark:text-snow-10">
    <div class="flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-4">
        <div class="grow py-1 text-sm">&copy; Copyright 2015 - {{ date('Y') }} by Tom Witkowski</div>
        <ul class="list-inline flex flex-row space-x-2">
            <li>
                <a
                    href="https://twitter.com/devgummibeer"
                    target="_blank"
                    rel="noreferrer noopener"
                    class="block p-1 hover:text-brand"
                    title="Twitter"
                >
                    <x-icon class="fab fa-twitter" />
                    <span class="sr-only">Twitter</span>
                </a>
            </li>
            <li>
                <a
                    href="https://github.com/Gummibeer"
                    target="_blank"
                    rel="noreferrer noopener"
                    class="block p-1 hover:text-brand"
                    title="GitHub"
                >
                    <x-icon class="fab fa-github" />
                    <span class="sr-only">GitHub</span>
                </a>
            </li>
            <li>
                <a
                    href="https://strava.com/athletes/22896286"
                    target="_blank"
                    rel="noreferrer noopener"
                    class="block p-1 hover:text-brand"
                    title="Strava"
                >
                    <x-icon class="fab fa-strava" />
                    <span class="sr-only">Strava</span>
                </a>
            </li>
            <li>
                <a
                    href="https://steamcommunity.com/id/gummibeer"
                    target="_blank"
                    rel="noreferrer noopener"
                    class="block p-1 hover:text-brand"
                    title="Steam"
                >
                    <x-icon class="fab fa-steam" />
                    <span class="sr-only">Steam</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="mt-4 flex flex-col space-y-4 sm:mt-2 sm:flex-row sm:justify-between sm:space-y-0 sm:space-x-4">
        <ul class="list-inline flex flex-col space-y-2 text-xs sm:flex-row sm:space-y-0 sm:space-x-4">
            <li>
                <x-icon class="fal fa-mobile mr-1" />
                <a
                    href="tel:+491621525105"
                    class="hover:text-brand"
                    >+49 162 1525105</a
                >
            </li>
            <li>
                <x-icon class="fal fa-at mr-1" />
                <a
                    href="mailto:dev@gummibeer.de"
                    class="hover:text-brand"
                    >dev@gummibeer.de</a
                >
            </li>
            <li>
                <x-icon class="fab fa-telegram-plane mr-1" />
                <a
                    href="https://t.me/gummibeer"
                    class="hover:text-brand"
                >
                    @gummibeer
                </a>
            </li>
        </ul>
        <ul class="list-inline flex flex-row space-x-4 text-xs">
            <li>
                <a
                    href="{{ url('/imprint') }}"
                    class="hover:text-brand"
                    >Imprint</a
                >
            </li>
            <li>
                <a
                    href="{{ url('/privacy') }}"
                    class="hover:text-brand"
                    >Privacy</a
                >
            </li>
        </ul>
    </div>
</footer>
