<statamic:collection:streams
    sort="date:desc"
    paginate="12"
    as="streams"
>
    @push ('head')
        <x-link-pagination :paginate="$paginate" />
    @endpush

    <x-section>
        <h1 class="mb-8 text-6xl leading-none font-black text-night-0">Streams</h1>
        <div class="mb-8 grid grid-flow-row grid-cols-1 gap-4 md:grid-cols-2 md:gap-8 lg:grid-cols-3 lg:gap-10 xl:gap-12">
            @foreach ($streams as $stream)
                <x-stream.preview :stream="$stream" />
            @endforeach
        </div>
        <x-pagination :paginate="$paginate" />
    </x-section>
</statamic:collection:streams>
