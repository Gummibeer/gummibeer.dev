<statamic:collection:streams
    sort="date:desc"
    limit="3"
    as="streams"
>
    <x-section>
        <div class="mb-8 flex items-baseline justify-between gap-4">
            <h2 class="text-4xl leading-none font-bold text-night-0">Latest Streams</h2>
            <a
                href="/streams"
                class="font-bold text-brand hover:underlined"
                >all streams</a
            >
        </div>
        <x-grid>
            @foreach ($streams as $stream)
                <x-stream.preview :stream="$stream" />
            @endforeach
        </x-grid>
    </x-section>
</statamic:collection:streams>
