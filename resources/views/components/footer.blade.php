@props (['identity'])

<footer class="w-full bg-white px-4 py-4 text-snow-20 md:px-8 md:py-6 lg:px-10 xl:px-12 dark:bg-night-10 dark:text-snow-10">
    <div class="flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-4">
        <div class="grow py-1 text-sm">&copy; Copyright {{ $identity?->copyright_since }} - {{ date('Y') }} by {{ $identity?->copyright_name }}</div>
        <ul class="list-inline flex flex-row space-x-2">
            <li>
                <a
                    href="{{ $identity?->github_url }}"
                    target="_blank"
                    rel="noreferrer noopener"
                    class="block p-1 hover:text-brand"
                    title="GitHub"
                    ><x-icon name="fab-github" /><span class="sr-only">GitHub</span></a
                >
            </li>
            <li>
                <a
                    href="{{ $identity?->steam_url }}"
                    target="_blank"
                    rel="noreferrer noopener"
                    class="block p-1 hover:text-brand"
                    title="Steam"
                    ><x-icon name="fab-steam" /><span class="sr-only">Steam</span></a
                >
            </li>
        </ul>
    </div>
    <div class="mt-4 flex flex-col space-y-4 sm:mt-2 sm:flex-row sm:justify-between sm:space-y-0 sm:space-x-4">
        <ul class="list-inline flex flex-col space-y-2 text-xs sm:flex-row sm:space-y-0 sm:space-x-4">
            <li>
                <x-icon
                    name="ski-phone"
                    class="mr-1"
                />
                <a
                    href="tel:{{ str_replace([' ', '-', '(', ')'], '', $identity?->phone ?? '') }}"
                    class="hover:text-brand"
                    >{{ $identity?->phone }}</a
                >
            </li>
            <li>
                <x-icon
                    name="ski-at-sign"
                    class="mr-1"
                />
                <a
                    href="mailto:{{ $identity?->email }}"
                    class="hover:text-brand"
                    >{{ $identity?->email }}</a
                >
            </li>
        </ul>
        <ul class="list-inline flex flex-row space-x-4 text-xs">
            <li>
                <a
                    href="{{ $identity?->contact_page?->permalink }}"
                    class="hover:text-brand"
                    >{{ $identity?->contact_label }}</a
                >
            </li>
            <li>
                <a
                    href="{{ $identity?->imprint_page?->permalink }}"
                    class="hover:text-brand"
                    >{{ $identity?->imprint_label }}</a
                >
            </li>
            <li>
                <a
                    href="{{ $identity?->privacy_page?->permalink }}"
                    class="hover:text-brand"
                    >{{ $identity?->privacy_label }}</a
                >
            </li>
        </ul>
    </div>
</footer>
