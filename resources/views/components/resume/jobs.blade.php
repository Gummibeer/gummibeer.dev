<x-section class="bg-dotted">
    <div class="mx-auto w-full sm:max-w-screen-sm sm:px-4 md:max-w-screen-md md:px-0">
        <div class="divide-y overflow-hidden rounded-4 bg-white px-4 shadow dark:bg-night-20">
            @foreach ($jobs as $job)
                <x-resume.job :job="$job" />
            @endforeach
        </div>
    </div>
</x-section>
