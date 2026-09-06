<statamic:collection:posts
    sort="date:desc"
    paginate="12"
    as="posts"
>
    @push ('head')
        <x-link-pagination :paginate="$paginate" />
    @endpush

    <x-section>
        <h1 class="mb-8 text-6xl leading-none font-black text-night-0">Blog</h1>
        <x-post.search />
        <div class="mb-8 grid grid-flow-row grid-cols-1 gap-4 md:grid-cols-2 md:gap-8 lg:grid-cols-3 lg:gap-10 xl:gap-12">
            @foreach ($posts as $post)
                <x-post.preview :post="$post" />
            @endforeach
        </div>
        <x-pagination :paginate="$paginate" />
    </x-section>
</statamic:collection:posts>
