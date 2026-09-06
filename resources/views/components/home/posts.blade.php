<statamic:collection:posts
    sort="date:desc"
    limit="4"
    as="posts"
>
    <div class="py-8">
        <div class="mx-auto w-full sm:max-w-screen-sm md:max-w-screen-md lg:max-w-screen-lg">
            <div class="mb-8 flex items-baseline justify-between gap-4">
                <h2 class="text-4xl leading-none font-bold text-night-0">Latest Posts</h2>
                <a
                    href="/blog"
                    class="font-bold text-brand hover:underlined"
                >
                    all posts
                </a>
            </div>
            @if ($posts->isNotEmpty())
                <x-post.promo :post="$posts->first()" />
            @endif
        </div>

        @if ($posts->skip(1)->isNotEmpty())
            <x-section class="bg-dotted">
                <x-grid>
                    @foreach ($posts->skip(1) as $post)
                        <x-post.preview
                            :post="$post"
                            :class="$loop->iteration === 3 ? 'hidden lg:block' : ''"
                        />
                    @endforeach
                </x-grid>
            </x-section>
        @endif
    </div>
</statamic:collection:posts>
