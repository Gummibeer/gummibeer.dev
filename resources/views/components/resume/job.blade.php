<div class="py-4 @if($job->has_end) text-snow-20 dark:text-snow-10 @endif">
    <div class="flex flex-row sm:items-center sm:space-x-4">
        @if ($job->logo)
            <div class="hidden h-24 w-24 sm:block">
                <x-img
                    :src="$job->logo"
                    :alt="$job->title"
                    class="h-full w-full object-contain"
                />
            </div>
        @endif
        <div class="grow">
            <div class="flex flex-col items-center justify-between sm:flex-row">
                <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4">
                    <h3 class="text-2xl @if(!$job->has_end) font-medium text-brand @endif">
                        <x-icon :name="$job->icon_class" />
                        {{ $job->title }}
                    </h3>
                    <a
                        href="{{ $job->website }}"
                        target="_blank"
                        class="inline-block p-1 text-xs text-snow-20 hover:text-brand dark:text-snow-10"
                    >
                        {{ $job->website_host }}
                    </a>
                </div>
                <aside class="text-right text-sm">
                    <div>
                        <time datetime="{{ $job->start_at->toIso8601String() }}">{{ $job->start_at->year }}</time>
                        -
                        <time datetime="{{ ($job->end_at ?? now())->toIso8601String() }}">{{ optional($job->end_at)->year ?? 'now' }}</time>
                    </div>
                    <span class="text-snow-20 dark:text-snow-10">{{ Illuminate\Support\Str::money($job->salary ?? 0) }}</span>
                </aside>
            </div>
            <strong class="block @if(!$job->has_end) font-bold @else text-sm font-normal @endif">{{ $job->role }}</strong>
            <ul class="flex list-none space-x-4 @if(!$job->has_end) text-sm @else text-xs @endif mt-1">
                @foreach ($job->stack as $tool)
                    <li>{{ $tool }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
