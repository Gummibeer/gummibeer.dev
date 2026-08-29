<?php /** @var Illuminate\View\ComponentAttributeBag $attributes */ ?>
<?php /** @var Illuminate\Support\HtmlString $slot */ ?>
<?php /** @var App\Post $post */ ?>

<article {{ $attributes->except('post')->merge(['class' => 'rounded-4 shadow bg-white dark:bg-night-20 overflow-hidden']) }}>
    @if($post->image)
    <a href="{{ $post->url }}">
        <x-img
            src="{{ $post->image }}"
            width="768"
            ratio="21:9"
            :alt="$post->title"
            :crop="true"/>
    </a>
    @endif
    <div class="p-4">
        @if($post->categories()->isNotEmpty())
            <x-post.ul-categories :post="$post" class="mb-4"/>
        @endif
        <h3 class="mb-4 text-2xl font-bold leading-none text-night-0 dark:text-white">
            <a href="{{ $post->url }}" class="hover:underlined">
                {{ $post->title }}
            </a>
        </h3>
        <x-post.aside :post="$post" class="mb-4 text-sm"/>
        <p>{{ $post->description }}</p>
    </div>
</article>