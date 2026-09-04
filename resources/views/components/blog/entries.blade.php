<statamic:collection
    from="posts|streams"
    sort="date:desc"
    paginate="12"
    as="entries"
>
    @push ('head')
        <x-link-pagination :paginate="$paginate" />
        <x-link-feed route="blog.feed" />
    @endpush

    <x-section>
        <h1 class="mb-8 text-6xl leading-none font-black text-night-0 dark:text-white">Blog</h1>
        <x-post.search />
        <div class="mb-8 grid grid-flow-row grid-cols-1 gap-4 md:grid-cols-2 md:gap-8 lg:grid-cols-3 lg:gap-10 xl:gap-12">
            @foreach ($entries as $entry)
                @if ($entry->collection()->handle() === 'posts')
                    <x-post.preview :post="$entry" />
                @elseif ($entry->collection()->handle() === 'streams')
                    <x-stream.preview :stream="$entry" />
                @endif
            @endforeach
        </div>
        <x-pagination :paginate="$paginate" />
    </x-section>
</statamic:collection>
