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
        class="w-full rounded-1 border-b-2 border-night-10 bg-white px-4 py-2 shadow focus:border-brand focus:outline-none dark:border-snow-10 dark:bg-night-10"
    />
    <ol
        class="list-none space-y-2"
        :class="{ hidden: results.length == 0 }"
    >
        <template x-for="result in results">
            <li class="overflow-hidden rounded-1 bg-white p-4 shadow dark:bg-night-20">
                <a
                    :href="result.url"
                    class="group block"
                >
                    <div class="flex justify-between space-x-2 sm:justify-start">
                        <strong
                            x-text="result.title"
                            class="group-hover:text-brand"
                        ></strong>
                        <span class="text-snow-20 dark:text-snow-10">
                            <x-icon class="fal fa-calendar mr-1" />
                            <time x-text="result.date"></time>
                        </span>
                    </div>
                    <p class="truncate" x-text="result.description"></p>
                </a>
            </li>
        </template>
    </ol>
</div>
