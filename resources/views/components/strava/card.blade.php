<div class="flex flex-row items-center space-x-4 overflow-hidden rounded-4 bg-white p-4 shadow dark:bg-night-20">
    <x-icon :class="'fa-solid fa-3x text-snow-20 dark:text-snow-10 '.$icon" />
    <div class="grow">
        <span class="block text-xl">{{ $label }}</span>
        <div>
            <strong class="font-mono text-3xl text-brand">{{ round($value) }}</strong>
            <span class="mb-4 text-snow-20 dark:text-snow-10">{{ $unit }}</span>
        </div>
    </div>
</div>
