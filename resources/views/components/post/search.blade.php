<form
    action="{{ route('blog.search') }}"
    method="GET"
    role="search"
    class="mb-4 flex gap-2 md:mb-8 lg:mb-10 xl:mb-12"
>
    <label
        for="blog-search"
        class="sr-only"
    >
        Search blog
    </label>
    <input
        id="blog-search"
        type="search"
        name="q"
        value="{{ request('q') }}"
        placeholder="Search &mldr;"
        autocomplete="off"
        minlength="3"
        class="min-w-0 flex-1 rounded-1 border-b-2 border-night-10 bg-white px-4 py-2 shadow focus:border-brand focus:outline-none"
    />
    <button
        type="submit"
        class="rounded-1 bg-brand px-4 py-2 font-bold text-night-0 shadow transition hover:brightness-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand focus-visible:ring-offset-2"
    >
        Search
    </button>
</form>
