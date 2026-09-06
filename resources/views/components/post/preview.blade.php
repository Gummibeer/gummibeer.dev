<?php /** @var Illuminate\View\ComponentAttributeBag $attributes */ ?>
<?php /** @var Illuminate\Support\HtmlString $slot */ ?>
<?php /** @var Statamic\Contracts\Entries\Entry $post */ ?>

<article {{ $attributes->except('post')->merge(['class' => 'rounded-4 shadow bg-white overflow-hidden']) }}>
    @if ($post->image)
        <a href="{{ $post->permalink }}">
            <x-img
                :src="$post->image"
                width="768"
                ratio="21:9"
                :alt="$post->title"
                :crop="true"
            />
        </a>
    @endif
    <div class="p-4">
        @if ($post->category)
            <x-post.category
                :category="$post->category"
                class="mb-4"
            />
        @endif
        <h3 class="mb-4 text-2xl leading-none font-bold text-night-0">
            <a
                href="{{ $post->permalink }}"
                class="hover:underlined"
            >
                {{ $post->title }}
            </a>
        </h3>
        <x-post.aside
            :post="$post"
            class="mb-4 text-sm"
        />
        <p>{{ $post->description }}</p>
    </div>
</article>
