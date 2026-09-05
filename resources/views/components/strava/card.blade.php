<div class="flex flex-row items-center space-x-4 overflow-hidden rounded-4 bg-white p-4 shadow">
    <x-icon
        :name="$icon"
        class="text-5xl text-snow-20"
    />
    <div class="grow">
        <span class="block text-xl">{{ $label }}</span>
        <div>
            <strong class="font-mono text-3xl text-brand">{{ round($value) }}</strong>
            <span class="mb-4 text-snow-20">{{ $unit }}</span>
        </div>
    </div>
</div>
