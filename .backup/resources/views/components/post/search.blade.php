<div
    class="mb-4 space-y-2 md:mb-8 lg:mb-10 xl:mb-12"
    x-data="window.search"
>
    <input
        type="search"
        name="search"
        placeholder="Search &mldr;"
        autocomplete="off"
        @input.debounce.250ms="search"
        x-model="query"
        class="py-2 px-4 w-full bg-white border-b-2 shadow dark:bg-night-10 border-night-10 dark:border-snow-10 rounded-1 focus:outline-none focus:border-brand"
    />
    <ol class="space-y-2 list-none" :class="{'hidden': results.length == 0}">
        <template x-for="result in results">
            <li class="overflow-hidden p-4 bg-white shadow rounded-1 dark:bg-night-20">
                <a :href="result.url" class="block group">
                    <div class="flex justify-between space-x-2 sm:justify-start">
                        <strong x-text="result.title" class="group-hover:text-brand"></strong>
                        <span class="text-snow-20 dark:text-snow-10">
                            <x-icon class="mr-1 fal fa-calendar"/>
                            <time x-text="result.date"></time>
                        </span>
                    </div>
                    <p class="truncate" x-text="result.description"></p>
                </a>
            </li>
        </template>
    </ol>
</div>