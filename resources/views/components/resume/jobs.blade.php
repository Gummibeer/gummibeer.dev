@php
    $jobs = Statamic\Facades\Entry::query()
        ->where('collection', 'jobs')
        ->whereStatus('published')
        ->get()
        ->sort(function ($a, $b): int {
            $aHasEnd = filled($a->value('end_at'));
            $bHasEnd = filled($b->value('end_at'));

            if ($aHasEnd === $bHasEnd) {
                return Carbon\Carbon::parse($b->value('start_at'))->timestamp <=> Carbon\Carbon::parse($a->value('start_at'))->timestamp;
            }

            return $aHasEnd ? 1 : -1;
        })
        ->values();
@endphp

<x-section class="bg-dotted">
    <div class="mx-auto w-full sm:max-w-screen-sm sm:px-4 md:max-w-screen-md md:px-0">
        <div class="divide-y overflow-hidden rounded-4 bg-white px-4 shadow dark:bg-night-20">
            @foreach ($jobs as $job)
                <x-resume.job :job="$job" />
            @endforeach
        </div>
    </div>
</x-section>
