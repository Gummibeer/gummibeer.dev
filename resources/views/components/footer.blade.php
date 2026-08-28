<?php /** @var App\Services\SiteIdentity $identity */ ?>

<footer class="w-full bg-white px-4 py-4 text-snow-20 md:px-8 md:py-6 lg:px-10 xl:px-12 dark:bg-night-10 dark:text-snow-10">
    <div class="flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-4">
        <div class="grow py-1 text-sm">
            &copy; Copyright {{ $identity->copyrightSince() }} - {{ date('Y') }} by {{ $identity->copyrightName() }}
        </div>
        <ul class="list-inline flex flex-row space-x-2">
            <li>
                <a
                    href="{{ $identity->twitterUrl() }}"
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
                    href="{{ $identity->githubUrl() }}"
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
                    href="{{ $identity->stravaUrl() }}"
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
                    href="{{ $identity->steamUrl() }}"
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
                    href="{{ $identity->phoneUrl() }}"
                    class="hover:text-brand"
                    >{{ $identity->phone() }}</a
                >
            </li>
            <li>
                <x-icon class="fal fa-at mr-1" />
                <a
                    href="{{ $identity->emailUrl() }}"
                    class="hover:text-brand"
                    >{{ $identity->email() }}</a
                >
            </li>
            <li>
                <x-icon class="fab fa-telegram-plane mr-1" />
                <a
                    href="{{ $identity->telegramUrl() }}"
                    class="hover:text-brand"
                >
                    {{ $identity->telegramLabel() }}
                </a>
            </li>
        </ul>
        <ul class="list-inline flex flex-row space-x-4 text-xs">
            <li>
                <a
                    href="{{ $identity->imprintUrl() }}"
                    class="hover:text-brand"
                    >{{ $identity->imprintLabel() }}</a
                >
            </li>
            <li>
                <a
                    href="{{ $identity->privacyUrl() }}"
                    class="hover:text-brand"
                    >{{ $identity->privacyLabel() }}</a
                >
            </li>
        </ul>
    </div>
</footer>
