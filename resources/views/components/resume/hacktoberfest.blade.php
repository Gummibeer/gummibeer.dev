@php
    $hacktoberfests = Statamic\Facades\Entry::query()
        ->where('collection', 'hacktoberfest')
        ->whereStatus('published')
        ->get()
        ->sortByDesc(fn ($entry) => $entry->slug());
@endphp

<x-section>
    <div class="mx-auto prose mb-8 w-full sm:max-w-screen-sm sm:px-4 md:prose-lg md:max-w-screen-md md:px-0 lg:prose-xl">
        <h2>Hacktoberfest</h2>
        <p>A monthlong celebration of open source software hosted by DigitalOcean, Intel and DEV. Hacktoberfest is open to everyone in the global community. All backgrounds and skill levels are encouraged to complete the challenge.</p>
        <small class="block"><a href="https://hacktoberfest.digitalocean.com" target="_blank">hacktoberfest.digitalocean.com</a></small>
    </div>
    <x-grid class="xl:grid-cols-4">
        @foreach ($hacktoberfests as $hacktoberfest)
            <div class="overflow-hidden rounded-4 bg-white shadow dark:bg-night-20">
                <x-img :src="$hacktoberfest->image" width="1024" height="512" :alt="$hacktoberfest->name" />
                <div class="px-4 py-2">
                    <strong class="block">{{ $hacktoberfest->name }}</strong>
                    <p class="text-sm text-snow-20 dark:text-snow-10">{{ $hacktoberfest->description }}</p>
                </div>
            </div>
        @endforeach
    </x-grid>
</x-section>
