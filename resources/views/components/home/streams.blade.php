<statamic:collection:streams
    sort="date:desc"
    limit="3"
    as="streams"
>
    <x-section>
        <h2 class="mb-8 text-4xl leading-none font-bold text-night-0 dark:text-white">Latest Streams</h2>
        <x-grid>
            @foreach ($streams as $stream)
                <x-stream.preview :stream="$stream" />
            @endforeach
        </x-grid>
    </x-section>
</statamic:collection:streams>
