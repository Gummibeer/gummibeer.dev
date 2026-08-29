<x-section>
    <h2 class="mb-8 text-4xl leading-none font-bold text-night-0 dark:text-white">Latest Streams</h2>
    <x-grid>
        @foreach ($streams->take(3) as $stream)
            <x-stream.preview :stream="$stream" />
        @endforeach
    </x-grid>
</x-section>
