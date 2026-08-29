<footer class="py-4 px-4 w-full bg-white dark:bg-night-10 md:py-6 md:px-8 lg:px-10 xl:px-12 text-snow-20 dark:text-snow-10">
    <div class="flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-4">
        <div class="flex-grow py-1 text-sm">
            &copy; Copyright 2015 - {{ date('Y') }} by Tom Witkowski
        </div>
        <ul class="flex flex-row space-x-2 list-inline">
            <li>
                <a
                    href="https://twitter.com/devgummibeer"
                    target="_blank"
                    rel="noreferrer noopener"
                    class="block p-1 hover:text-brand"
                    title="Twitter"
                >
                    <x-icon class="fab fa-twitter"/>
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
                    <x-icon class="fab fa-github"/>
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
                    <x-icon class="fab fa-strava"/>
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
                    <x-icon class="fab fa-steam"/>
                    <span class="sr-only">Steam</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="flex flex-col mt-4 space-y-4 sm:flex-row sm:justify-between sm:space-y-0 sm:space-x-4 sm:mt-2">
        <ul class="flex flex-col space-y-2 text-xs sm:flex-row list-inline sm:space-y-0 sm:space-x-4">
            <li>
                <x-icon class="mr-1 fal fa-mobile"/>
                <a href="tel:+491621525105" class="hover:text-brand">+49 162 1525105</a>
            </li>
            <li>
                <x-icon class="mr-1 fal fa-at"/>
                <a href="mailto:dev@gummibeer.de" class="hover:text-brand">dev@gummibeer.de</a>
            </li>
            <li>
                <x-icon class="mr-1 fab fa-telegram-plane"/>
                <a href="https://t.me/gummibeer" class="hover:text-brand">@gummibeer</a>
            </li>
        </ul>
        <ul class="flex flex-row space-x-4 text-xs list-inline">
            <li><a href="https://t.me/GummibeerDev" class="hover:text-brand">Newsletter</a></li>
            <li><a href="{{ route('imprint') }}" class="hover:text-brand">Imprint</a></li>
            <li><a href="{{ route('privacy') }}" class="hover:text-brand">Privacy</a></li>
        </ul>
    </div>
</footer>