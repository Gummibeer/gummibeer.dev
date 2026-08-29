<div class="flex overflow-hidden flex-row items-center p-4 space-x-4 bg-white shadow rounded-4 dark:bg-night-20">
    <x-icon :class="'fal fa-3x text-snow-20 dark:text-snow-10 '.$icon"/>
    <div class="flex-grow">
        <span class="block text-xl">{{ $label }}</span>
        <div>
            <strong class="font-mono text-3xl text-brand">{{ round($value) }}</strong>
            <span class="mb-4 text-snow-20 dark:text-snow-10">{{ $unit }}</span>
        </div>
    </div>
</div>