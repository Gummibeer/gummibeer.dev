@if ($posts->isNotEmpty())
    <x-section>
        <x-post.promo :post="$posts->first()" />
    </x-section>
@endif

@if ($posts->skip(1)->isNotEmpty())
    <x-section class="bg-dotted">
        <h2 class="mb-8 text-4xl leading-none font-bold text-night-0 dark:text-white">Latest Posts</h2>
        <x-grid>
            @foreach ($posts->skip(1)->take(3) as $post)
                <x-post.preview
                    :post="$post"
                    :class="$loop->iteration === 3 ? 'hidden lg:block' : ''"
                />
            @endforeach
        </x-grid>
    </x-section>
@endif
